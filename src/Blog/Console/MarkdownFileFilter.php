<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Console;

use FilterIterator;
use InvalidArgumentException;
use Override;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Traversable;

use function is_dir;
use function sprintf;

/**
 * Usage:
 *
 * <code>
 * $files = new MarkdownFileFilter($path);
 * </code>
 *
 * @template-extends FilterIterator<mixed, string, Traversable<mixed, string>>
 */
final class MarkdownFileFilter extends FilterIterator
{
    public function __construct(string $dir = '.')
    {
        if (! is_dir($dir)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a valid directory name; received "%s"',
                $dir
            ));
        }

        $dir      = new RecursiveDirectoryIterator($dir);
        $iterator = new RecursiveIteratorIterator($dir);

        parent::__construct($iterator);

        $this->rewind();
    }

    #[Override]
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
