<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use Mezzio\Handler\NotFoundHandler;
use Psr\Container\ContainerInterface;

class FeedHandlerFactory
{
    public function __invoke(ContainerInterface $container): FeedHandler
    {
        $notFoundHandler = $container->get(NotFoundHandler::class);
        assert($notFoundHandler instanceof NotFoundHandler);

        return new FeedHandler($notFoundHandler);
    }
}
