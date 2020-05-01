<?php

declare(strict_types=1);

namespace App\FrontMatter;

use Spatie\YamlFrontMatter\YamlFrontMatter;

final class Parser implements ParserInterface
{
    public function parse(string $file): DocumentInterface
    {
        return new Document(YamlFrontMatter::parseFile($file));
    }
}
