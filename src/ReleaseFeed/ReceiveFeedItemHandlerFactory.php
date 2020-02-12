<?php

declare(strict_types=1);

namespace GetLaminas\ReleaseFeed;

use League\CommonMark\CommonMarkConverter;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class ReceiveFeedItemHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ReceiveFeedItemHandler
    {
        return new ReceiveFeedItemHandler(
            $container->get('config')['release-feed']['feed-file'],
            new CommonMarkConverter(),
            $container->get(ResponseFactoryInterface::class),
            $container->get(ProblemDetailsResponseFactory::class)
        );
    }
}
