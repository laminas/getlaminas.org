<?php

declare(strict_types=1);

namespace App\ContentParser;

use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

final class Document implements DocumentInterface
{
    public function __construct(
        private readonly RenderedContentWithFrontMatter $renderedContent,
        private readonly ?string $tableOfContents
    ) {
    }

    public function getFrontMatter(): array
    {
        return (array)$this->renderedContent->getFrontMatter();
    }

    public function getTableOfContents(): ?string
    {
        return $this->tableOfContents;
    }

    public function getContent(): string
    {
        return $this->renderedContent->getContent();
    }
}
