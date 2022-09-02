<?php // phpcs:disable WebimpressCodingStandard.NamingConventions.Trait.Suffix


declare(strict_types=1);

namespace GetLaminas\Blog;

use App\ContentParser\Parser;
use App\ContentParser\ParserInterface;
use DateTime;
use DateTimeZone;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

use function explode;
use function file_exists;
use function is_array;
use function is_numeric;
use function sprintf;
use function trim;

trait CreateBlogPostFromDataArray
{
    private string $authorDataRootPath = 'data/blog/authors';

    private ?ParserInterface $contentParser = null;

    /**
     * Delimiter between post summary and extended body
     */
    private string $postDelimiter = '<!--- EXTENDED -->';

    public function setAuthorDataRootPath(string $path): void
    {
        $this->authorDataRootPath = $path;
    }

    private function getContentParser(): ParserInterface
    {
        if (null === $this->contentParser) {
            $this->contentParser = new Parser();
        }

        return $this->contentParser;
    }

    private function createBlogPostFromDataArray(array $post): BlogPost
    {
        if (! isset($post['path'])) {
            throw new RuntimeException(sprintf(
                'Blog data provided does not include a "path" element; cannot create %s instance',
                BlogPost::class
            ));
        }

        $document = $this->getContentParser()->parse($post['path']);
        $post     = $document->getFrontMatter();
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
            ? new DateTime('@' . $dateString, new DateTimeZone('America/Chicago'))
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
