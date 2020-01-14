<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;

class FeedHandler implements RequestHandlerInterface
{
    /**
     * @var string
     */
    private $feedPath;

    /**
     * @var RequestHandlerInterface
     */
    private $notFoundHandler;

    public function __construct(RequestHandlerInterface $notFoundHandler, string $feedPath = 'var/blog/feeds')
    {
        $this->notFoundHandler = $notFoundHandler;
        $this->feedPath        = $feedPath;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
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

    private function getTagFeedPath(string $tag, string $type) : string
    {
        return sprintf(
            '%s/%s.%s.xml',
            $this->feedPath,
            str_replace([' ', '%20'], '+', $tag),
            $type
        );
    }

    private function getFeedPath(string $type) : string
    {
        return sprintf('%s/%s.xml', $this->feedPath, $type);
    }
}
