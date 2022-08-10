<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Console;

use FilterIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function is_dir;
use function sprintf;

/**
 * Usage:
 *
 * <code>
 * $files = new MarkdownFileFilter($path);
 *
 * // or
 * $dir   = new DirectoryIterator($path);
 * $files = new MarkdownFileIterator($dir);
 *
 * // or
 * $dir   = new RecursiveDirectoryIterator($path);
 * $files = new MarkdownFileIterator($dir);
 * </code>
 */
class MarkdownFileFilter extends FilterIterator
{
    public function __construct(string $dirOrIterator = '.')
    {
        if (! is_dir($dirOrIterator)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a valid directory name; received "%s"',
                $dirOrIterator
            ));
        }

        $dirOrIterator = new RecursiveDirectoryIterator($dirOrIterator);
        $iterator      = new RecursiveIteratorIterator($dirOrIterator);

        parent::__construct($iterator);

        $this->rewind();
    }

    public function accept(): bool
    {
        $current = $this->getInnerIterator()->current();
        if (! $current instanceof SplFileInfo) {
            return false;
        }

        if (! $current->isFile()) {
            return false;
        }

        $ext = $current->getExtension();
        if ($ext !== 'md') {
            return false;
        }

        return true;
    }
}
