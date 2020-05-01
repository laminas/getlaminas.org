<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace GetLaminas\Blog;

use App\FrontMatter\Parser;
use App\FrontMatter\ParserInterface;
use DateTime;
use DateTimezone;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

trait CreateBlogPostFromDataArray
{
    /** @var string */
    private $authorDataRootPath = 'data/blog/authors';

    /** @var ParserInterface */
    private $frontMatterParser;

    /**
     * Delimiter between post summary and extended body
     *
     * @var string
     */
    private $postDelimiter = '<!--- EXTENDED -->';

    public function setAuthorDataRootPath(string $path): void
    {
        $this->authorDataRootPath = $path;
    }

    private function getFrontMatterParser(): ParserInterface
    {
        if (! $this->frontMatterParser) {
            $this->frontMatterParser = new Parser();
        }

        return $this->frontMatterParser;
    }

    private function createBlogPostFromDataArray(array $post): BlogPost
    {
        if (! isset($post['path'])) {
            throw new RuntimeException(sprintf(
                'Blog data provided does not include a "path" element; cannot create %s instance',
                BlogPost::class
            ));
        }

        $document = $this->getFrontMatterParser()->parse($post['path']);
        $post     = $document->getYAML();
        $parts    = explode($this->postDelimiter, $document->getContent(), 2);
        $created  = $this->createDateTimeFromString($post['created']);
        $updated  = $post['updated'] && $post['updated'] !== $post['created']
            ? $this->createDateTimeFromString($post['updated'])
            : $created;

        return new BlogPost(
            $post['id'],
            $post['title'],
            $this->createAuthorFromAuthorName($post['author']),
            $created,
            $updated,
            is_array($post['tags'])
                ? $post['tags']
                : explode('|', trim((string) $post['tags'], '|')),
            $parts[0],
            $parts[1] ?? '',
            (bool) $post['draft'],
            (bool) $post['public']
        );
    }

    private function createDateTimeFromString(string $dateString): DateTime
    {
        return is_numeric($dateString)
            ? new DateTime('@' . $dateString, new DateTimezone('America/Chicago'))
            : new DateTime($dateString);
    }

    private function createAuthorFromAuthorName(string $authorName): BlogAuthor
    {
        $filename = sprintf('%s/%s.yml', $this->authorDataRootPath, $authorName);
        if (! file_exists($filename)) {
            return new BlogAuthor($authorName, '', '', '');
        }

        $metadata = Yaml::parseFile($filename);
        return new BlogAuthor(
            $authorName,
            $metadata['name'] ?? 'no-reply@getlaminas.org',
            $metadata['email'] ?? '',
            $metadata['uri'] ?? ''
        );
    }
}
