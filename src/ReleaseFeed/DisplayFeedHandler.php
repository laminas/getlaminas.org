<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DisplayFeedHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly StreamFactoryInterface $streamFactory,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly string $feedFile
    ) {
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $this->streamFactory->createStreamFromFile($this->feedFile, 'r');
        return $this->responseFactory->createResponse(200)
            ->withBody($body)
            ->withHeader('Content-Type', 'application/rss+xml');
    }
}
