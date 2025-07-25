<?php

declare(strict_types=1);

namespace GetLaminas\Integration;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use GetLaminas\Integration\Enums\IntegrationCategoryEnum;
use GetLaminas\Integration\Enums\IntegrationTypeEnum;

use function explode;
use function is_array;
use function is_numeric;
use function trim;

trait CreateIntegrationFromArrayTrait
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
    protected function createIntegrationFromArray(array $packageData): ?Integration
    {
        $category = IntegrationCategoryEnum::tryFrom(trim($packageData['category']));
        $type     = IntegrationTypeEnum::tryFrom(trim($packageData['type']));

        if ($category === null || $type === null) {
            return null;
        }

        $created = $this->createDateTimeFromString((string) $packageData['created']);
        $updated = $packageData['updated'] && $packageData['updated'] !== $packageData['created']
            ? $this->createDateTimeFromString((string) $packageData['updated'])
            : $created;

        return new Integration(
            $packageData['id'],
            $packageData['name'],
            $type,
            $packageData['packagistUrl'],
            $packageData['repository'],
            (bool) $packageData['abandoned'],
            $packageData['description'],
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
