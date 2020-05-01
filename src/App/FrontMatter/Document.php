<?php

declare(strict_types=1);

namespace App\FrontMatter;

use League\CommonMark\CommonMarkConverter;
use Spatie\YamlFrontMatter\Document as SpatieDocument;

final class Document implements DocumentInterface
{
    /** @var SpatieDocument */
    private $document;

    public function __construct(SpatieDocument $document)
    {
        $this->document = $document;
    }

    public function getYAML(): array
    {
        return $this->document->matter();
    }

    public function getContent(): string
    {
        return (new CommonMarkConverter())->convertToHtml($this->document->body());
    }
}
