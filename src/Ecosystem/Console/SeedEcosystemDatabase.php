<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Console;

use CurlHandle;
use DateTimeImmutable;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function assert;
use function base64_decode;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function explode;
use function is_array;
use function is_string;
use function json_decode;
use function realpath;
use function sprintf;

use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;

class SeedEcosystemDatabase extends Command
{
    public const string PACKAGE_UPDATE_TIME = '6 hours ago';

    private CurlHandle $curl;
    private CurlHandle $githubCurl;
    private ?string $ghToken = null;
    public PdoMapper $mapper;

    private string $update = 'UPDATE packages SET
            name = %s,
            repository = %s,
            abandoned = %d,
            description = %s,
            updated = %d,
            downloads = %d,
            stars = %d,
            issues = %d,
            image = %s
            WHERE id = %s;';

    public function setMapper(PdoMapper $mapper): void
    {
        $this->mapper = $mapper;
    }

    protected function configure(): void
    {
        $this->setName('ecosystem:seed-db');
        $this->setDescription('Generate and seed the "ecosystem packages" database.');
        $this->setHelp('Re-create the ecosystem packages database from the package entities.');

        $this->addOption(
            'db-path',
            'b',
            InputOption::VALUE_REQUIRED,
            'Path to the database file, relative to the --path.',
            sprintf('%s/%s', CreateEcosystemDatabase::PACKAGES_DB_PATH, CreateEcosystemDatabase::PACKAGES_DB_FILE)
        );

        $this->addOption(
            'github-token',
            'gt',
            InputOption::VALUE_OPTIONAL,
            'GitHub access token',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
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

        /** @var array{id: string, name: string, updated: int}|null $packagesDueUpdates */
        $packagesDueUpdates = $this->mapper->fetchPackagesDueUpdates(
            new DateTimeImmutable(self::PACKAGE_UPDATE_TIME)
        );
        if (empty($packagesDueUpdates)) {
            $io->success('No packages need updates');

            return 0;
        }

        $io->title('Updating ecosystem packages database');

        $pdo = realpath($dbFile);
        assert($pdo !== false);

        $pdo = new PDO('sqlite:' . $pdo);

        $pdo->beginTransaction();
        $this->initCurl();

        foreach ($packagesDueUpdates as $package) {
            $packageData = $this->getPackageData($package);
            $this->updatePackage($packageData, $pdo);
        }

        $pdo->commit();

        $io->success('Updated ecosystem packages database');

        return 0;
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
            'User-Agent: getlaminas.org',
        ];

        $this->githubCurl = curl_init();
        curl_setopt($this->githubCurl, CURLOPT_HTTPHEADER, $githubHeaders);
        curl_setopt($this->githubCurl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->githubCurl, CURLOPT_RETURNTRANSFER, 1);
    }

    private function getPackageData(array $package): array
    {
        $packagistUrl = sprintf(
            'https://repo.packagist.org/packages/%s.json',
            $package['name'],
        );
        curl_setopt($this->curl, CURLOPT_URL, $packagistUrl);
        $rawResult = curl_exec($this->curl);
        assert(is_string($rawResult));
        $packagistResult = json_decode($rawResult, true);
        assert(is_array($packagistResult));

        /**
         * @var array{
         *     name: string,
         *     repository: string,
         *     abandoned: string,
         *     description: string,
         *     updated: int,
         *     stars: int,
         *     issues: int,
         *     downloads: array{total: int, monthly: int, daily: int},
         *     id: string
         *  } $packageData
         */
        $packageData = $packagistResult['package'];

        return [
            'name'        => $packageData['name'],
            'repository'  => $packageData['repository'],
            'abandoned'   => (int) isset($packageData['abandoned']),
            'description' => $packageData['description'],
            'updated'     => (new DateTimeImmutable())->getTimestamp(),
            'stars'       => (int) $packageData['github_stars'],
            'issues'      => (int) $packageData['github_open_issues'],
            'downloads'   => (int) $packageData['downloads']['total'],
            'image'       => $this->getSocialPreview($packageData['name']),
            'id'          => $package['id'],
        ];
    }

    private function getSocialPreview(string $package): ?string
    {
        $packageId    = explode('/', $package);
        $graphQlQuery = sprintf(
            '{"query": "query {repository(owner: \"%s\", name: \"%s\"){openGraphImageUrl}}"}',
            $packageId[0],
            $packageId[1]
        );

        curl_setopt($this->githubCurl, CURLOPT_URL, 'https://api.github.com/graphql');
        curl_setopt($this->githubCurl, CURLOPT_POST, true);
        curl_setopt($this->githubCurl, CURLOPT_POSTFIELDS, $graphQlQuery);

        $rawResult = curl_exec($this->githubCurl);
        assert(is_string($rawResult));

        $githubResult = json_decode($rawResult, true);

        return $githubResult['data']['repository']['openGraphImageUrl'] ?? null;
    }

    /**
     * @phpcs:ignore
     * @param array{
     *     name: string,
     *     repository: string,
     *     abandoned: string,
     *     description: string,
     *     updated: int,
     *     stars: int,
     *     issues: int,
     *     downloads: array{total: int, monthly: int, daily: int},
     *     image: string|null
     *     id: string
     *    } $packageData
     */
    private function updatePackage(array $packageData, PDO $pdo): void
    {
        $statement = sprintf(
            $this->update,
            $pdo->quote($packageData['name']),
            $pdo->quote($packageData['repository']),
            $packageData['abandoned'],
            $pdo->quote($packageData['description']),
            $packageData['updated'],
            $packageData['downloads'],
            $packageData['stars'],
            $packageData['issues'],
            $packageData['image'] !== null ? $pdo->quote($packageData['image']) : '0',
            $pdo->quote($packageData['id'])
        );

        $pdo->exec($statement);
    }
}
