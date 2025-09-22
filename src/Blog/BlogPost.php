<?php

declare(strict_types=1);

namespace GetLaminas\Blog;

use DateTimeInterface;

final class BlogPost
{
    /**
     * @param string[] $tags
     */
    public function __construct(
        public string $id,
        public string $title,
        public BlogAuthor $author,
        public DateTimeInterface $created,
        public ?DateTimeInterface $updated,
        public array $tags,
        public string $body,
        public string $extended,
        public ?string $toc,
        public bool $isDraft,
        public bool $isPublic,
        public ?string $openGraphImage = null,
        public ?string $openGraphDescription = null
    ) {
    }
}
