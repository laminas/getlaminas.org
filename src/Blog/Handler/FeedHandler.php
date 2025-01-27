<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function file_exists;
use function fopen;
use function sprintf;
use function str_replace;

class FeedHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly RequestHandlerInterface $notFoundHandler,
        private readonly string $feedPath = 'var/blog/feeds'
    ) {
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tag  = $request->getAttribute('tag');
        $type = $request->getAttribute('type', 'rss');
        $path = $tag
            ? $this->getTagFeedPath($tag, $type)
            : $this->getFeedPath($type);

        if (! file_exists($path)) {
            return $this->notFoundHandler->handle($request);
        }

        return (new Response())
            ->withHeader('Content-Type', sprintf('application/%s+xml', $type))
            ->withBody(new Stream(fopen($path, 'r')));
    }

    private function getTagFeedPath(string $tag, string $type): string
    {
        return sprintf(
            '%s/%s.%s.xml',
            $this->feedPath,
            str_replace([' ', '%20'], '+', $tag),
            $type
        );
    }

    private function getFeedPath(string $type): string
    {
        return sprintf('%s/%s.xml', $this->feedPath, $type);
    }
}
