<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FeedRedirectHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ServerUrlHelper */
    private $serverUrl;

    /** @var UrlHelper */
    private $url;

    public function __construct(
        UrlHelper $url,
        ServerUrlHelper $serverUrl,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->url             = $url;
        $this->serverUrl       = $serverUrl;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse(301)
            ->withHeader(
                'Location',
                $this->serverUrl->generate($this->url->generate(
                    'blog.feed',
                    ['type' => $request->getAttribute('type', 'rss')]
                ))
            );
    }
}
