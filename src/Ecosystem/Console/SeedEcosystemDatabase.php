<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Console;

use CurlHandle;
use DateTimeImmutable;
use GetLaminas\Ecosystem\EcosystemConnectionTrait;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function assert;
use function curl_exec;
use function curl_setopt;
use function is_array;
use function is_string;
use function json_decode;
use function realpath;
use function sprintf;
use function str_replace;

use const CURLOPT_URL;

class SeedEcosystemDatabase extends Command
{
    use EcosystemConnectionTrait;

    /**
     * Packagist's API caches the response for 12 hours
     */
    public const string PACKAGE_UPDATE_TIME = '12 hours ago';

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

        /**
         * @var array{
         *     id: string,
         *     name: string,
         *     updated: int
         * } $package
         */
        foreach ($packagesDueUpdates as $package) {
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
             *     downloads: int,
             *     image: string,
             *     id: string
             *    } $packageData
             */
            $packageData = $this->getPackageData($package);
            $this->updatePackage($packageData, $pdo);
        }

        $pdo->commit();

        $io->success('Updated ecosystem packages database');

        return 0;
    }

    /**
     * @phpcs:ignore
     * @param array{
     *     id: string,
     *     name: string,
     *     updated: int
     * } $package
     */
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

        return [
            'name'        => $packageData['name'],
            'repository'  => $packageData['repository'],
            'abandoned'   => (int) isset($packageData['abandoned']),
            'description' => $packageData['description'],
            'updated'     => (new DateTimeImmutable())->getTimestamp(),
            'stars'       => $packageData['github_stars'],
            'issues'      => $packageData['github_open_issues'],
            'downloads'   => $packageData['downloads']['total'],
            'image'       => $this->getPackageImage(
                str_replace('https://github.com/', '', $packageData['repository'])
            ),
            'id'          => $package['id'],
        ];
    }

    /**
     * @phpcs:ignore
     * @param array{
     *     name: string,
     *     repository: string,
     *     abandoned: int,
     *     description: string,
     *     updated: int,
     *     stars: int,
     *     issues: int,
     *     downloads: int,
     *     image: string,
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
            $pdo->quote($packageData['image']),
            $pdo->quote($packageData['id'])
        );

        $pdo->exec($statement);
    }
}
