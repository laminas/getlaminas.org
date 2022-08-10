<?php

declare(strict_types=1);

namespace App\FrontMatter;

interface ParserInterface
{
    public function parse(string $file): DocumentInterface;
}
