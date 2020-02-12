<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class DisplayFeedHandlerFactory
{
    public function __invoke(ContainerInterface $container) : DisplayFeedHandler
    {
        return new DisplayFeedHandler(
            $container->get(StreamFactoryInterface::class),
            $container->get(ResponseFactoryInterface::class),
            $container->get('config')['release-feed']['feed-file']
        );
    }
}
