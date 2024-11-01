<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem;

use DateTimeInterface;

class EcosystemPackage
{
    public function __construct(
        public string $id,
        public string $name,
        public string $packagistUrl,
        public string $repository,
        public bool $abandoned,
        public string $description,
        public string $license,
        public DateTimeInterface $created,
        public DateTimeInterface $updated,
        public array $categories,
        public array $tags,
        public string $website,
        public int $downloads,
        public int $stars,
        public int $forks,
        public int $watchers,
        public int $issues
    ) {
    }
}
