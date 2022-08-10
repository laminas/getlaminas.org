<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function assert;
use function is_string;

class DisplayFeedHandlerFactory
{
    public function __invoke(ContainerInterface $container): DisplayFeedHandler
    {
        $streamFactory = $container->get(StreamFactoryInterface::class);
        assert($streamFactory instanceof StreamFactoryInterface);

        $responseFactory = $container->get(ResponseFactoryInterface::class);
        assert($responseFactory instanceof ResponseFactoryInterface);

        $feedFile = $container->get('config')['release-feed']['feed-file'] ?? '';
        assert(is_string($feedFile) && '' !== $feedFile);

        return new DisplayFeedHandler($streamFactory, $responseFactory, $feedFile);
    }
}
