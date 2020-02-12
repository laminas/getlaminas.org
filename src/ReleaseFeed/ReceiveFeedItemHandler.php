<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use DateTimeImmutable;
use Exception;
use Laminas\Feed\Reader\Reader;
use Laminas\Feed\Writer\Feed;
use League\CommonMark\CommonMarkConverter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseFactoryInterface;

class ReceiveFeedItemHandler implements RequestHandlerInterface
{
    /** @var string */
    private $feedFile;

    /** @var CommonMarkConverter */
    private $markdown;

    /** @var ProblemDetailsResponseFactory */
    private $problemFactory;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    public function __construct(
        string $feedFile,
        CommonMarkConverter $markdown,
        ResponseFactoryInterface $responseFactory,
        ProblemDetailsResponseFactory $problemFactory
    ) {
        $this->feedFile        = $feedFile;
        $this->markdown        = $markdown;
        $this->responseFactory = $responseFactory;
        $this->problemFactory  = $problemFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (! $this->validateData($data)) {
            return $this->problemFactory->createResponse(
                $request,
                203,
                'Malformed release provided'
            );
        }

        $releases = $this->getCurrentReleases();
        $releases->push($this->createRelease($data));

        try {
            $this->writeFeedToPath($this->createFeed($releases));
        } catch (Exception $e) {
            return $this->problemFactory->createResponse(
                $request,
                500,
                sprintf('Error generating release feed: %s', $e->getMessage())
            );
        }

        return $this->responseFactory->createResponse(204);
    }

    private function validateData(array $data) : bool
    {
        return isset(
            $data['package'],
            $data['version'],
            $data['url'],
            $data['changelog'],
            $data['publication_date'],
            $data['author_name'],
            $data['author_url']
        );
    }

    private function createRelease(array $data) : Release
    {
        return new Release(
            $data['package'],
            $data['version'],
            $data['url'],
            $this->markdown->convertToHtml($data['changelog']),
            new DateTimeImmutable($data['publication_date']),
            new Author($data['author_name'], $data['author_url'])
        );
    }

    private function getCurrentReleases() : Releases
    {
        $xml      = file_get_contents($this->feedFile);
        $feed     = Reader::importString($xml);
        $releases = new Releases();

        foreach ($feed as $entry) {
            $title = $entry->getTitle();
            list($package, $version) = explode(' ', $title);

            $author = $entry->getAuthor();
            $author = new Author($author['name'], $author['uri']);

            $date = $entry->getDateCreated();

            $releases->push(new Release(
                $package,
                $version,
                $entry->getLink(),
                $entry->getContent(),
                $date,
                $author
            ));
        }

        return $releases;
    }

    private function createFeed(Releases $releases): Feed
    {
        $feed = new Feed();
        $feed->setTitle('Laminas Project Releases');
        $feed->setLink('https://getlaminas.org');
        $feed->addAuthor([
            'name' => 'Laminas Project',
            'uri'  => 'https://getlaminas.org',
        ]);
        $feed->setDescription('Laminas Project releases, including components, MVC, API Tools, and Mezzio');

        $latest = false;
        foreach ($releases as $release) {
            $latest = $latest ?: $release->date;

            $entry = $feed->createEntry();
            $entry->setTitle(sprintf('%s %s', $release->package, $release->version));
            $entry->setLink($release->url);
            $entry->addAuthor($release->author->toArray());
            $entry->setDateCreated($release->date);
            $entry->setDateModified($release->date);
            $entry->setDescription(sprintf('Release information for %s %s', $release->package, $release->version));
            $entry->setContent($release->content);

            $feed->addEntry($entry);
        }

        $feed->setDateModified($latest);
        $feed->setLastBuildDate($latest);

        return $feed;
    }

    private function writeFeedToPath(Feed $feed): void
    {
        file_put_contents($this->feedFile, $feed->export('rss'), LOCK_EX);
    }
}
