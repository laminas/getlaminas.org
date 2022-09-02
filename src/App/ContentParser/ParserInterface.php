<?php

declare(strict_types=1);

namespace App\ContentParser;

interface ParserInterface
{
    public function parse(string $file): DocumentInterface;
}
