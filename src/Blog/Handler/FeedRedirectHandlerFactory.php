<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class FeedRedirectHandlerFactory
{
    public function __invoke(ContainerInterface $container): FeedRedirectHandler
    {
        return new FeedRedirectHandler(
            $container->get(UrlHelper::class),
            $container->get(ServerUrlHelper::class),
            $container->get(ResponseFactoryInterface::class)
        );
    }
}
