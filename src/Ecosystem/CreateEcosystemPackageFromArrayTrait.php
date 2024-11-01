<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

use function explode;
use function is_array;
use function is_numeric;
use function trim;

trait CreateEcosystemPackageFromArrayTrait
{
    /**
     * @throws Exception
     * @phpcs:ignore
     * @param array{
     *    id: string,
     *    name: string,
     *    repository: string,
     *    description: string,
     *    created: int,
     *    updated: int,
     *    forks: int,
     *    watchers: int,
     *    stars: int,
     *    issues: int,
     *    downloads: int,
     *    abandoned: int,
     *    packagistUrl: string,
     *    categories: string|array<string>,
     *    website: string,
     *    license: string,
     *    tags: string|array<string>
     *    } $packageData
     */
    private function createEcosystemPackageFromArray(array $packageData): EcosystemPackage
    {
        $created = $this->createDateTimeFromString((string) $packageData['created']);
        $updated = $packageData['updated'] && $packageData['updated'] !== $packageData['created']
            ? $this->createDateTimeFromString((string) $packageData['updated'])
            : $created;

        return new EcosystemPackage(
            $packageData['id'],
            $packageData['name'],
            $packageData['packagistUrl'],
            $packageData['repository'],
            (bool) $packageData['abandoned'],
            $packageData['description'],
            $packageData['license'],
            $created,
            $updated,
            is_array($packageData['categories'])
                ? $packageData['categories']
                : explode('|', trim($packageData['categories'], '|')),
            is_array($packageData['tags'])
                ? $packageData['tags']
                : explode('|', trim($packageData['tags'], '|')),
            $packageData['website'] ?? '',
            $packageData['downloads'],
            $packageData['stars'],
            $packageData['forks'],
            $packageData['watchers'],
            $packageData['issues']
        );
    }

    /**
     * @throws Exception
     */
    private function createDateTimeFromString(string $dateString): DateTimeImmutable
    {
        return is_numeric($dateString)
            ? new DateTimeImmutable('@' . $dateString, new DateTimeZone('America/Chicago'))
            : new DateTimeImmutable($dateString);
    }
}
