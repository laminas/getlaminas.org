<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Console;

use CurlHandle;
use DateTimeImmutable;
use GetLaminas\Ecosystem\CreateEcosystemPackageFromArrayTrait;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_values;
use function assert;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function implode;
use function is_array;
use function is_string;
use function json_decode;
use function realpath;
use function sprintf;
use function strtolower;

use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;

class SeedEcosystemDatabase extends Command
{
    use CreateEcosystemPackageFromArrayTrait;

    public const string PACKAGE_UPDATE_TIME = '6 hours ago';

    private CurlHandle $curl;
    public PdoMapper $mapper;

    private string $update = 'UPDATE packages SET
            name = %s,
            repository = %s,
            abandoned = %d,
            description = %s,
            license = %s,
            updated = %d,
            tags = %s,
            downloads = %d,
            stars = %d,
            forks = %d,
            watchers = %d,
            issues = %d
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
            CreateEcosystemDatabase::PACKAGES_DB_PATH
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $dbFile = $input->getOption('db-path');
        assert(is_string($dbFile));

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
         *     forks: int,
         *     watchers: int,
         *     stars: int,
         *     issues: int,
         *     downloads: array{total: int, monthly: int, daily: int},
         *     license: string,
         *     tags: array<string>|string,
         *     id: string
         *  } $packageData
         */
        $packageData     = $packagistResult['package'];
        $lastVersionData = array_values($packageData['versions'])[0];

        return [
            'name'        => $packageData['name'],
            'repository'  => $packageData['repository'],
            'abandoned'   => (int) isset($packageData['abandoned']),
            'description' => $packageData['description'],
            'updated'     => (new DateTimeImmutable())->getTimestamp(),
            'forks'       => (int) $packageData['github_forks'],
            'watchers'    => (int) $packageData['github_watchers'],
            'stars'       => (int) $packageData['github_stars'],
            'issues'      => (int) $packageData['github_open_issues'],
            'downloads'   => (int) $packageData['downloads']['total'],
            'license'     => ! empty($lastVersionData['license']) ? $lastVersionData['license'][0] : '',
            'tags'        => ! empty($lastVersionData['keywords']) ? $lastVersionData['keywords'] : '',
            'id'          => $package['id'],
        ];
    }

    /**
     * @phpcs:ignore
     * @param array{
     *    name: string,
     *    repository: string,
     *    abandoned: string,
     *    description: string,
     *    updated: int,
     *    forks: int,
     *    watchers: int,
     *    stars: int,
     *    issues: int,
     *    downloads: array{total: int, monthly: int, daily: int},
     *    license: string,
     *    tags: array<string>|string,
     *    id: string
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
            $pdo->quote($packageData['license']),
            $packageData['updated'],
            ! empty($packageData['tags'])
                ? $pdo->quote(strtolower(sprintf('|%s|', implode('|', $packageData['tags']))))
                : $pdo->quote(''),
            $packageData['downloads'],
            $packageData['stars'],
            $packageData['forks'],
            $packageData['watchers'],
            $packageData['issues'],
            $pdo->quote($packageData['id'])
        );

        $pdo->exec($statement);
    }
}
