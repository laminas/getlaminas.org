<?php

declare(strict_types=1);

namespace App\FrontMatter;

interface DocumentInterface
{
    public function getYAML(): array;

    public function getContent(): string;
}
