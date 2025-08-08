<?php

declare(strict_types=1);

namespace GetLaminas\Integration;

use DateTimeInterface;
use GetLaminas\Integration\Enums\IntegrationTypeEnum;

class Integration
{
    /**
     * @param array<array-key, string> $keywords
     */
    public function __construct(
        public string $id,
        public string $name,
        public IntegrationTypeEnum $type,
        public string $packagistUrl,
        public string $repository,
        public bool $abandoned,
        public string $description,
        public DateTimeInterface $created,
        public DateTimeInterface $updated,
        public array $keywords,
        public string $website,
        public int $downloads,
        public int $stars,
        public int $issues,
        public string $image,
    ) {
    }
}
