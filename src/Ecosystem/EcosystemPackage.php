<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem;

use DateTimeInterface;
use GetLaminas\Ecosystem\Enums\EcosystemCategoryEnum;
use GetLaminas\Ecosystem\Enums\EcosystemTypeEnum;

class EcosystemPackage
{
    /**
     * @param array<array-key, string> $keywords
     */
    public function __construct(
        public string $id,
        public string $name,
        public EcosystemTypeEnum $type,
        public string $packagistUrl,
        public string $repository,
        public bool $abandoned,
        public string $description,
        public DateTimeInterface $created,
        public DateTimeInterface $updated,
        public EcosystemCategoryEnum $category,
        public array $keywords,
        public string $website,
        public int $downloads,
        public int $stars,
        public int $issues,
        public string $image,
    ) {
    }
}
