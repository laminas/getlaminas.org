<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use Mezzio\Handler\NotFoundHandler;
use Psr\Container\ContainerInterface;

class FeedHandlerFactory
{
    public function __invoke(ContainerInterface $container): FeedHandler
    {
        return new FeedHandler(
            $container->get(NotFoundHandler::class)
        );
    }
}
