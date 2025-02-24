<?php

declare(strict_types=1);

namespace App\ContentParser;

interface DocumentInterface
{
    /**
     * @return array{
     *     id: string,
     *     author: string,
     *     title: string,
     *     draft: bool,
     *     public: bool,
     *     created: string,
     *     updated: string,
     *     openGraphImage: string,
     *     openGraphDescription: string,
     *     tags: list<string>
     * }
     */
    public function getFrontMatter(): array;

    public function getTableOfContents(): ?string;

    public function getContent(): string;
}
