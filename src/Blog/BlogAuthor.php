<?php

declare(strict_types=1);

namespace GetLaminas\Blog;

final class BlogAuthor
{
    public function __construct(
        public string $username,
        public string $fullName,
        public string $email,
        public string $url
    ) {
    }
}
