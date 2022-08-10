<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Mapper;

use GetLaminas\Blog\BlogPost;
use Laminas\Paginator\Paginator;

interface MapperInterface
{
    public function fetch(string $id): ?BlogPost;

    public function fetchAll(): Paginator;

    public function fetchAllByAuthor(string $author): Paginator;

    public function fetchAllByTag(string $tag): Paginator;

    public function search(string $toMatch): ?array;
}
