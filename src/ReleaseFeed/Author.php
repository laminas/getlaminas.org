<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

class Author
{
    public function __construct(
        public readonly string $name,
        public readonly string $uri,
    ) {
        $this->name = $name;
        $this->uri  = $uri;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'uri'  => $this->uri,
        ];
    }
}
