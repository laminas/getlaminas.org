<?php

declare(strict_types=1);

namespace App\ContentParser;

interface DocumentInterface
{
    public function getFrontMatter(): array;

    public function getTableOfContents(): ?string;

    public function getContent(): string;
}
