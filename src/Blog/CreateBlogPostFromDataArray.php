<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace GetLaminas\Blog;

use DateTime;
use DateTimezone;
use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Parser;
use RuntimeException;

trait CreateBlogPostFromDataArray
{
    /** @var Parser */
    private $parser;

    /**
     * Delimiter between post summary and extended body
     *
     * @var string
     */
    private $postDelimiter = '<!--- EXTENDED -->';

    private function getParser() : Parser
    {
        if (! $this->parser) {
            $this->parser = new Parser(null, new CommonMarkParser());
        }

        return $this->parser;
    }

    private function createBlogPostFromDataArray(array $post) : BlogPost
    {
        if (! isset($post['path'])) {
            throw new RuntimeException(sprintf(
                'Blog data provided does not include a "path" element; cannot create %s instance',
                BlogPost::class
            ));
        }

        $parser   = $this->getParser();
        $document = $parser->parse(file_get_contents($post['path']));
        $post     = $document->getYAML();
        $parts    = explode($this->postDelimiter, $document->getContent(), 2);
        $created  = $this->createDateTimeFromString($post['created']);
        $updated  = $post['updated'] && $post['updated'] !== $post['created']
            ? $this->createDateTimeFromString($post['updated'])
            : $created;

        return new BlogPost(
            $post['id'],
            $post['title'],
            $post['author'],
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

    private function createDateTimeFromString(string $dateString) : DateTime
    {
        return is_numeric($dateString)
            ? new DateTime('@' . $dateString, new DateTimezone('America/Chicago'))
            : new DateTime($dateString);
    }
}
