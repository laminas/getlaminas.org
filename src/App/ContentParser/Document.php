<?php

declare(strict_types=1);

namespace App\ContentParser;

use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Override;

final readonly class Document implements DocumentInterface
{
    public function __construct(
        private RenderedContentWithFrontMatter $renderedContent,
        private ?string $tableOfContents
    ) {
    }

    #[Override]
    public function getFrontMatter(): array
    {
        return (array) $this->renderedContent->getFrontMatter();
    }

    #[Override]
    public function getTableOfContents(): ?string
    {
        return $this->tableOfContents;
    }

    #[Override]
    public function getContent(): string
    {
        return $this->renderedContent->getContent();
    }
}
