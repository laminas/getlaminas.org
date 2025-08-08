<?php

declare(strict_types=1);

namespace GetLaminas\Integration\Mapper;

use DateTimeImmutable;
use GetLaminas\Integration\Integration;
use Laminas\Paginator\Paginator;

interface MapperInterface
{
    /** @return Paginator<int, Integration> */
    public function fetchAll(): Paginator;

    /**
     * @param  array<int, string> $keywords
     * @return Paginator<int, Integration>
     */
    public function fetchAllByFilters(array $keywords, ?string $type = null, ?string $search = null): Paginator;

    /** @return Paginator<int, Integration> */
    public function fetchAllByKeyword(string $keyword): Paginator;

    public function getPackagesTitles(): array;

    public function deletePackageByName(string $package): bool;

    public function fetchPackagesDueUpdates(DateTimeImmutable $updated): ?array;
}
