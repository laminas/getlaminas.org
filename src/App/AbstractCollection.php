<?php

declare(strict_types=1);

namespace App;

use App\ContentParser\ParserInterface;
use RuntimeException;

use function file_exists;
use function file_put_contents;
use function glob;
use function uasort;
use function var_export;

use const LOCK_EX;

abstract class AbstractCollection
{
    /** @var string */
    protected const FOLDER_COLLECTION = '';

    /** @var string */
    protected const CACHE_FILE = '';

    protected array $collection = [];

    public function __construct(protected ParserInterface $contentParser)
    {
        if (empty(static::CACHE_FILE)) {
            throw new RuntimeException('The cache file path is not defined!');
        }

        if (! file_exists(static::CACHE_FILE)) {
            $this->buildCache();
            return;
        }

        $this->collection = require static::CACHE_FILE;
    }

    public function getAll(): array
    {
        return $this->collection;
    }

    public function getFromFile(string $file): array
    {
        $result = [];
        if (file_exists($file)) {
            $doc            = $this->contentParser->parse($file);
            $result         = $doc->getFrontMatter();
            $result['body'] = $doc->getContent();
        }
        return $result;
    }

    protected function buildCache(): void
    {
        if (empty(static::FOLDER_COLLECTION)) {
            throw new RuntimeException('The folder collection is not defined!');
        }

        foreach (glob(static::FOLDER_COLLECTION . '/*.md') as $file) {
            $doc                     = $this->contentParser->parse($file);
            $fields                  = $doc->getFrontMatter();
            $this->collection[$file] = $fields;
        }

        uasort($this->collection, [$this, 'order']);
        file_put_contents(static::CACHE_FILE, '<?php return ' . var_export($this->collection, true) . ';', LOCK_EX);
    }

    protected function order(array $a, array $b): bool|int
    {
        return false;
    }
}
