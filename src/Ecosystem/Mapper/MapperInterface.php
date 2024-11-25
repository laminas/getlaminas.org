<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Mapper;

use DateTimeImmutable;
use GetLaminas\Ecosystem\EcosystemPackage;
use Laminas\Paginator\Paginator;

interface MapperInterface
{
    /** @return Paginator<int, EcosystemPackage> */
    public function fetchAll(): Paginator;

    /** @return Paginator<int, EcosystemPackage> */
    public function fetchAllByFilters(array $filters, string $search = ''): Paginator;

    /** @return Paginator<int, EcosystemPackage> */
    public function fetchAllByKeyword(string $keyword): Paginator;

    public function search(string $toMatch): ?array;

    public function fetchPackagesDueUpdates(DateTimeImmutable $updated): ?array;
}
