<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

use function array_reverse;
use function array_slice;
use function count;
use function usort;

class Releases implements IteratorAggregate
{
    private array $releases = [];

    public function getIterator(): Iterator
    {
        $releases = $this->sort($this->releases);
        $releases = $this->truncate($releases);
        return new ArrayIterator($releases);
    }

    public function push(Release $release): void
    {
        $this->releases[] = $release;
    }

    private function sort(array $releases): array
    {
        usort($releases, function (Release $a, Release $b) {
            return $a->date <=> $b->date;
        });
        return array_reverse($releases);
    }

    private function truncate(array $releases): array
    {
        if (count($releases) < 30) {
            return $releases;
        }

        return array_slice($releases, 0, 30);
    }
}
