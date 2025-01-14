<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Console;

use CurlHandle;
use DateTimeImmutable;
use Exception;
use GetLaminas\Ecosystem\CreateEcosystemPackageFromArrayTrait;
use GetLaminas\Ecosystem\EcosystemConnectionTrait;
use GetLaminas\Ecosystem\EcosystemPackage;
use GetLaminas\Ecosystem\Handler\EcosystemHandler;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_diff;
use function array_key_first;
use function assert;
use function curl_exec;
use function curl_setopt;
use function file_exists;
use function file_get_contents;
use function filter_var;
use function getcwd;
use function implode;
use function is_array;
use function is_string;
use function json_decode;
use function preg_match;
use function realpath;
use function sprintf;
use function str_replace;
use function strtolower;
use function uniqid;
use function unlink;

use const CURLOPT_URL;
use const FILTER_VALIDATE_URL;

class CreateEcosystemDatabase extends Command
{
    use CreateEcosystemPackageFromArrayTrait;
    use EcosystemConnectionTrait;

    public const string PACKAGES_DB_PATH = 'data/ecosystem/database';
    public const string PACKAGES_DB_FILE = 'packages.db';

    private CurlHandle $curl;
    private CurlHandle $githubCurl;
    private ?string $ghToken     = null;
    private bool $forceRebuild   = false;
    private array $validPackages = [];
    public PdoMapper $mapper;

    /** @var string[] */
    private array $indices = [
        'CREATE INDEX keywords ON packages ( keywords )',
        'CREATE INDEX category ON packages ( category )',
        'CREATE INDEX "type" ON packages ( type )',
        'CREATE INDEX "usage" ON packages ( usage )',
        'CREATE INDEX package_name ON packages ( name )',
    ];

    private string $initial = 'INSERT INTO packages
        SELECT
            %s AS id,
            %s AS name,
            %s AS type,
            %s AS packagistUrl,
            %s AS repository,
            %d AS abandoned,
            %s AS description,
            %s AS "usage",
            %d AS created,
            %d AS updated,
            %s AS category,
            %s AS keywords,
            %s AS website,
            %d AS downloads,
            %d AS stars,
            %d AS issues,
            %s AS image
            ';

    private string $item = 'UNION SELECT
        %s,
        %s,
        %s,
        %s,
        %s,
        %d,
        %s,
        %s,
        %s,
        %d,
        %d,
        %s,
        %s,
        %s,
        %d,
        %d,
        %d,
        %s';

    private string $table = 'CREATE TABLE "packages" (
            id VARCHAR(255) NOT NULL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(255) NOT NULL,
            packagistUrl VARCHAR(255) NOT NULL,
            repository VARCHAR(255) NOT NULL,
            abandoned TINYINT NOT NULL,
            description TEXT NOT NULL,
            usage VARCHAR(255) NOT NULL,
            created UNSIGNED INTEGER NOT NULL,
            updated UNSIGNED INTEGER NOT NULL,
            category VARCHAR(255) NOT NULL,
            keywords TEXT,
            website VARCHAR(255),
            downloads UNSIGNED INTEGER,
            stars UNSIGNED INTEGER,
            issues UNSIGNED INTEGER,
            image VARCHAR(255) NOT NULL
        )';

    public function setMapper(PdoMapper $mapper): void
    {
        $this->mapper = $mapper;
    }

    protected function configure(): void
    {
        $this->setName('ecosystem:seed-db');
        $this->setDescription('Generate and seed the "ecosystem packages" database.');
        $this->setHelp('Re-create the blog post database from the post entities.');

        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Base path of the application; defaults to current working directory',
            realpath(getcwd())
        );

        $this->addOption(
            'data-path',
            'd',
            InputOption::VALUE_REQUIRED,
            'Path to the database file, relative to the --path.',
            EcosystemHandler::ECOSYSTEM_DIRECTORY
        );

        $this->addOption(
            'data-file',
            'f',
            InputOption::VALUE_REQUIRED,
            'Path to the blog posts, relative to the --path.',
            EcosystemHandler::ECOSYSTEM_FILE
        );

        $this->addOption(
            'db-path',
            'b',
            InputOption::VALUE_REQUIRED,
            'Path to the database file, relative to the --path.',
            sprintf('%s/%s', self::PACKAGES_DB_PATH, self::PACKAGES_DB_FILE)
        );

        $this->addOption(
            'github-token',
            'gt',
            InputOption::VALUE_OPTIONAL,
            'GitHub access token',
        );

        $this->addOption(
            'force-rebuild',
            'fr',
            InputOption::VALUE_OPTIONAL,
            'Regenerate database file from scratch',
            $this->forceRebuild
        );
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $basePath = $input->getOption('path');
        assert(is_string($basePath));
        $dataPath = $input->getOption('data-path');
        assert(is_string($dataPath));
        $dataFile = $input->getOption('data-file');
        assert(is_string($dataFile));
        $dbFile = $input->getOption('db-path');
        assert(is_string($dbFile));
        $this->ghToken = $input->getOption('github-token');

        $this->forceRebuild = $input->getOption('force-rebuild') !== false;

        $path = sprintf(
            '%s%s/%s',
            $basePath,
            $dataPath,
            $dataFile
        );

        $io->title('Generating ecosystem packages database');
        $userData = file_get_contents($path);
        assert(is_string($userData));

        $userDataArray = json_decode($userData, true);
        assert(is_array($userDataArray));

        $pdo = $this->createDatabase($dbFile);
        $this->initCurl();

        /** @var array{packagistUrl: string, keywords: array<string>, homepage: string, category: string, usage: string} $userData */
        foreach ($userDataArray as $userData) {
            $curlResult = $this->getPackageData($userData);
            if ($curlResult === null) {
                continue;
            }

            $package = $this->createEcosystemPackageFromArray($curlResult);
            if ($package === null) {
                continue;
            }

            $this->insertPackageInDatabase($package, $pdo);
        }

        if (! $this->forceRebuild) {
            $currentPackages = $this->mapper->getPackagesTitles();
            $removedPackages = array_diff($currentPackages, $this->validPackages);
            /** @var string $package */
            foreach ($removedPackages as $package) {
                $this->mapper->deletePackageByName($package);
            }
        }

        $io->success('Created ecosystem packages database');

        return 0;
    }

    public function createDatabase(string $path): PDO
    {
        if (file_exists($path) && file_get_contents($path) !== '') {
            if ($this->forceRebuild) {
                unlink($path);
            } else {
                return new PDO('sqlite:' . $path);
            }
        }

        if ($path[0] !== '/') {
            $path = realpath(getcwd()) . '/' . $path;
        }

        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        $pdo->exec($this->table);
        foreach ($this->indices as $index) {
            $pdo->exec($index);
        }

        $pdo->commit();

        return $pdo;
    }

    /**
     * @phpcs:ignore
     * @param array{
     *     packagistUrl: string,
     *      keywords: array<string>,
     *      homepage: string,
     *      category: string,
     *      usage: string
     * } $userData
     */
    private function getPackageData(array $userData): ?array
    {
        $urlComponents = [];
        preg_match('/packagist.org\/packages\/((?>\w-?)+\/(?>\w-?)+)/i', $userData['packagistUrl'], $urlComponents);

        if (! $this->forceRebuild) {
            $this->validPackages[] = $urlComponents[1];
            $existingPackage       = $this->mapper->searchPackage($urlComponents[1]);
            if ($existingPackage !== null && $existingPackage !== []) {
                return null;
            }
        }

        $packagistUrl = sprintf(
            'https://repo.packagist.org/packages/%s.json',
            $urlComponents[1]
        );
        curl_setopt($this->curl, CURLOPT_URL, $packagistUrl);
        $rawResult = curl_exec($this->curl);
        assert(is_string($rawResult));

        $packagistResult = json_decode($rawResult, true);
        assert(is_array($packagistResult));
        /**
         * @var array{
         *     name: string,
         *     description: string,
         *     time: string,
         *     maintainers: array<string>,
         *     versions: array<array>,
         *     type: string,
         *     repository: string,
         *     github_stars: int,
         *     github_watchers: int,
         *     github_forks: int,
         *     github_open_issues: int,
         *     language: string,
         *     abandoned: string,
         *     dependents: int,
         *     suggesters: int,
         *     downloads: array{total: int, monthly: int, daily: int},
         *     favers: int
         *  } $packageData
         */
        $packageData = $packagistResult['package'];

        if (isset($packageData['abandoned'])) {
            return null;
        }

        if (! $userData['homepage'] || ! filter_var($userData['homepage'], FILTER_VALIDATE_URL)) {
            $lastVersion = array_key_first($packageData['versions']);
            if ($lastVersion === null) {
                $website = '';
            } else {
                $lastVersionData = $packageData['versions'][$lastVersion];
                /** @var array<array-key, string> $lastVersionData */
                $website = $lastVersionData['homepage'] ?? '';
            }
        } else {
            $website = $userData['homepage'];
        }

        $timestamp = (new DateTimeImmutable())->getTimestamp();

        return [
            'id'           => uniqid($packageData['name']),
            'name'         => $packageData['name'],
            'type'         => $packageData['type'],
            'repository'   => $packageData['repository'],
            'description'  => $packageData['description'],
            'created'      => $timestamp,
            'updated'      => $timestamp,
            'stars'        => $packageData['github_stars'],
            'issues'       => $packageData['github_open_issues'],
            'downloads'    => $packageData['downloads']['total'],
            'abandoned'    => (int) isset($packageData['abandoned']),
            'usage'        => $userData['usage'],
            'category'     => $userData['category'],
            'packagistUrl' => $userData['packagistUrl'],
            'keywords'     => $userData['keywords'] !== [] ? $userData['keywords'] : '',
            'website'      => $website,
            'image'        => $this->getSocialPreview(
                str_replace('https://github.com/', '', $packageData['repository'])
            ),
        ];
    }

    private function insertPackageInDatabase(EcosystemPackage $package, PDO $pdo): void
    {
        $statement = sprintf(
            $this->initial,
            $pdo->quote($package->id),
            $pdo->quote($package->name),
            $pdo->quote($package->type->value),
            $pdo->quote($package->packagistUrl),
            $pdo->quote($package->repository),
            (int) $package->abandoned,
            $pdo->quote($package->description),
            $pdo->quote($package->usage->value),
            $package->created->getTimestamp(),
            $package->updated->getTimestamp(),
            $pdo->quote($package->category->value),
            ! empty($package->keywords)
                ? $pdo->quote(strtolower(sprintf('|%s|', implode('|', $package->keywords))))
                : '',
            $pdo->quote($package->website),
            $package->downloads,
            $package->stars,
            $package->issues,
            $pdo->quote($package->image),
        );
        $pdo->exec($statement);
    }
}
