<?php

declare(strict_types=1);

namespace App\FrontMatter;

use Spatie\YamlFrontMatter\YamlFrontMatter;

interface ParserInterface
{
    public function parse(string $file): DocumentInterface;
}
