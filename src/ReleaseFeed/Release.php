<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use DateTimeInterface;

final class Release
{
    public function __construct(
        public readonly string $package,
        public readonly string $version,
        public readonly string $url,
        public readonly string $content,
        public readonly DateTimeInterface $date,
        public readonly Author $author,
    ) {
    }
}
