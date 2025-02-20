<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use GetLaminas\Ecosystem\Enums\EcosystemCategoryEnum;
use GetLaminas\Ecosystem\Enums\EcosystemTypeEnum;
use GetLaminas\Ecosystem\Enums\EcosystemUsageEnum;

use function explode;
use function is_array;
use function is_numeric;
use function trim;

trait CreateEcosystemPackageFromArrayTrait
{
    /**
     * @phpcs:ignore
     * @param array{
     *     id: string,
     *     name: string,
     *     type: string,
     *     packagistUrl: string,
     *     repository: string,
     *     description: string,
     *     usage: string,
     *     created: int,
     *     updated: int,
     *     category: string,
     *     stars: int,
     *     issues: int,
     *     downloads: int,
     *     abandoned: int,
     *     keywords: string|array<string>,
     *     website: string,
     *     license: string,
     *     image: string|null
     * } $packageData
     * @throws Exception
     */
    protected function createEcosystemPackageFromArray(array $packageData): ?EcosystemPackage
    {
        $category = EcosystemCategoryEnum::tryFrom(trim($packageData['category']));
        $type     = EcosystemTypeEnum::tryFrom(trim($packageData['type']));
        $usage    = EcosystemUsageEnum::tryFrom(trim($packageData['usage']));

        if ($category === null || $type === null || $usage === null) {
            return null;
        }

        $created = $this->createDateTimeFromString((string) $packageData['created']);
        $updated = $packageData['updated'] && $packageData['updated'] !== $packageData['created']
            ? $this->createDateTimeFromString((string) $packageData['updated'])
            : $created;

        return new EcosystemPackage(
            $packageData['id'],
            $packageData['name'],
            $type,
            $packageData['packagistUrl'],
            $packageData['repository'],
            (bool) $packageData['abandoned'],
            $packageData['description'],
            $usage,
            $created,
            $updated,
            $category,
            is_array($packageData['keywords'])
                ? $packageData['keywords']
                : explode('|', trim($packageData['keywords'], '|')),
            $packageData['website'] ?? '',
            $packageData['downloads'],
            $packageData['stars'],
            $packageData['issues'],
            $packageData['image'] ?? '0'
        );
    }

    /**
     * @throws Exception
     */
    protected function createDateTimeFromString(string $dateString): DateTimeImmutable
    {
        return is_numeric($dateString)
            ? new DateTimeImmutable('@' . $dateString, new DateTimeZone('America/Chicago'))
            : new DateTimeImmutable($dateString);
    }
}
