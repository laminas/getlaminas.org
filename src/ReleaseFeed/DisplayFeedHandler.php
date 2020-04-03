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
    /** @var string */
    private $feedFile;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory,
        string $feedFile
    ) {
        $this->streamFactory   = $streamFactory;
        $this->responseFactory = $responseFactory;
        $this->feedFile        = $feedFile;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $this->streamFactory->createStreamFromFile($this->feedFile, 'r');
        return $this->responseFactory->createResponse(200)
            ->withBody($body)
            ->withHeader('Content-Type', 'application/rss+xml');
    }
}
