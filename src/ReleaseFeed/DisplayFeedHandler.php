<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DisplayFeedHandler implements RequestHandlerInterface
{
    public function __construct(
        private StreamFactoryInterface $streamFactory,
        private ResponseFactoryInterface $responseFactory,
        private string $feedFile
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $this->streamFactory->createStreamFromFile($this->feedFile, 'r');
        return $this->responseFactory->createResponse(200)
            ->withBody($body)
            ->withHeader('Content-Type', 'application/rss+xml');
    }
}
