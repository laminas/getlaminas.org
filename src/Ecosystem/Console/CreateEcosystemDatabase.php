<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Console;

use CurlHandle;
use DateTimeImmutable;
use Exception;
use GetLaminas\Ecosystem\CreateEcosystemPackageFromArrayTrait;
use GetLaminas\Ecosystem\EcosystemPackage;
use GetLaminas\Ecosystem\Handler\EcosystemHandler;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_values;
use function assert;
use function base64_decode;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function explode;
use function file_exists;
use function file_get_contents;
use function getcwd;
use function implode;
use function is_array;
use function is_string;
use function json_decode;
use function preg_match;
use function realpath;
use function sprintf;
use function strtolower;
use function uniqid;

use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;

class CreateEcosystemDatabase extends Command
{
    use CreateEcosystemPackageFromArrayTrait;

    public const string PACKAGES_DB_PATH = 'data/ecosystem/database';
    public const string PACKAGES_DB_FILE = 'packages.db';

    private CurlHandle $curl;
    private CurlHandle $githubCurl;
    private ?string $ghToken = null;
    public PdoMapper $mapper;

    /** @var string[] */
    private array $indices = [
        'CREATE INDEX tags ON packages ( tags )',
        'CREATE INDEX keywords ON packages ( keywords )',
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
            %s AS license,
            %d AS created,
            %d AS updated,
            %s AS category,
            %s AS keywords,
            %s AS tags,
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
        %s,
        %d,
        %s,
        %s,
        %d,
        %d,
        %s,
        %s,
        %s,
        %s,
        %d,
        %d,
        %d,
        %s';

    private string $searchTable = 'CREATE VIRTUAL TABLE search_packages USING FTS4(
            id,
            created,
            updated,
            name,
            tags,
            keywords
        )';

    private string $searchTrigger = 'CREATE TRIGGER after_packages_insert
            AFTER INSERT ON packages
            BEGIN
                INSERT INTO search_packages (
                    id,
                    created,
                    updated,
                    name,
                    tags,
                    keywords
                )
                VALUES (
                    new.id,
                    new.created,
                    new.updated,
                    new.name,
                    new.tags,
                    new.keywords
                );
            END
        ';

    private string $searchTriggerPostUpdate = 'CREATE TRIGGER after_packages_update
            AFTER UPDATE ON packages
            BEGIN
                UPDATE search_packages
                 SET 
                 id = new.id,
                 updated = new.updated,
                 name = new.name,
                 tags = new.tags,
                 keywords = new.keywords
                 WHERE id = new.id;
            END
        ';

    private string $table = 'CREATE TABLE "packages" (
            id VARCHAR(255) NOT NULL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(255) NOT NULL,
            packagistUrl VARCHAR(255) NOT NULL,
            repository VARCHAR(255) NOT NULL,
            abandoned TINYINT NOT NULL,
            description VARCHAR(255) NOT NULL,
            license VARCHAR(255) NOT NULL,
            created UNSIGNED INTEGER NOT NULL,
            updated UNSIGNED INTEGER NOT NULL,
            category VARCHAR(255) NOT NULL,
            keywords VARCHAR(255),
            tags VARCHAR(255),
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
        if (! $this->ghToken) {
            $variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), true);
            assert(is_array($variables));
            assert(isset($variables['REPO_TOKEN']));

            $this->ghToken = $variables['REPO_TOKEN'];
        }
        assert(is_string($this->ghToken));

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

        /** @var array{packagistUrl: string, githubUrl: string, categories: array, homepage: string} $userData */
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

        $io->success('Created ecosystem packages database');

        return 0;
    }

    private function createDatabase(string $path): PDO
    {
        if (file_exists($path) && file_get_contents($path) !== '') {
            $path = realpath($path);
            return new PDO('sqlite:' . $path);
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
        $pdo->exec($this->searchTable);
        $pdo->exec($this->searchTrigger);
        $pdo->exec($this->searchTriggerPostUpdate);

        $pdo->commit();

        return $pdo;
    }

    private function initCurl(): void
    {
        $headers = [
            'Accept: application/vnd.github+json',
            'X-GitHub-Api-Version: 2022-11-28',
            'User-Agent: getlaminas.org',
        ];

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

        $githubHeaders = [
            'Accept: application/vnd.github+json',
            'Authorization: Bearer ' . $this->ghToken,
            'X-GitHub-Api-Version: 2022-11-28',
            'User-Agent: jurj@rospace.com',
        ];

        $this->githubCurl = curl_init();
        curl_setopt($this->githubCurl, CURLOPT_HTTPHEADER, $githubHeaders);
        curl_setopt($this->githubCurl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->githubCurl, CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * @phpcs:ignore
     * @param array{
     *     packagistUrl: string,
     *     githubUrl: string,
     *     keywords: array<string>,
     *     homepage: string,
     *     category: string
     * } $userData
     */
    private function getPackageData(array $userData): ?array
    {
        $matches = [];
        preg_match('/packagist.org\/packages\/((?>\w-?)+\/(?>\w-?)+)/i', $userData['packagistUrl'], $matches);

        if (! isset($matches[1]) || ! empty($this->mapper->searchPackage($matches[1]))) {
            return null;
        }

        $packagistUrl = sprintf(
            'https://repo.packagist.org/packages/%s.json',
            $matches[1]
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

        $lastVersionData = array_values($packageData['versions'])[0];
        $timestamp       = (new DateTimeImmutable())->getTimestamp();

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
            'category'     => $userData['category'],
            'packagistUrl' => $userData['packagistUrl'],
            'keywords'     => $userData['keywords'] !== [] ? $userData['keywords'] : '',
            'website'      => isset($userData['homepage']) && $userData['homepage'] !== '' ? $userData['homepage']
                : $lastVersionData['homepage'] ?? '',
            'license'      => ! empty($lastVersionData['license']) ? $lastVersionData['license'][0] : '',
            'tags'         => ! empty($lastVersionData['keywords']) ? $lastVersionData['keywords'] : '',
            'image'        => $this->getSocialPreview($matches[1]),
        ];
    }

    private function getSocialPreview(string $package): ?string
    {
        $packageId    = explode('/', $package);
        $graphQlUrl   = 'https://api.github.com/graphql';
        $graphQlQuery = sprintf(
            '{"query": "query {repository(owner: \"%s\", name: \"%s\"){openGraphImageUrl}}"}',
            $packageId[0],
            $packageId[1]
        );
        curl_setopt($this->githubCurl, CURLOPT_URL, $graphQlUrl);
        curl_setopt($this->githubCurl, CURLOPT_POST, true);
        curl_setopt($this->githubCurl, CURLOPT_POSTFIELDS, $graphQlQuery);

        $rawResult = curl_exec($this->githubCurl);
        assert(is_string($rawResult));

        $githubResult = json_decode($rawResult, true);

        return $githubResult['data']['repository']['openGraphImageUrl'] ?? null;
    }

    private function insertPackageInDatabase(EcosystemPackage $package, PDO $pdo): void
    {
        $statement = sprintf(
            $this->initial,
            $pdo->quote($package->id),
            $pdo->quote($package->name),
            $pdo->quote($package->type),
            $pdo->quote($package->packagistUrl),
            $pdo->quote($package->repository),
            (int) $package->abandoned,
            $pdo->quote($package->description),
            $pdo->quote($package->license),
            $package->created->getTimestamp(),
            $package->updated->getTimestamp(),
            $pdo->quote($package->category->value),
            ! empty($package->keywords)
                ? $pdo->quote(strtolower(sprintf('|%s|', implode('|', $package->keywords))))
                : '',
            ! empty($package->tags)
                ? $pdo->quote(strtolower(sprintf('|%s|', implode('|', $package->tags))))
                : '',
            $pdo->quote($package->website),
            $package->downloads,
            $package->stars,
            $package->issues,
            $package->image !== null ? $pdo->quote($package->image) : 0,
        );

        $pdo->exec($statement);
    }
}
