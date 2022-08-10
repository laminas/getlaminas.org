<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use GetLaminas\Blog\Mapper\MapperInterface;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ListPostsHandlerFactory
{
    public function __invoke(ContainerInterface $container): ListPostsHandler
    {
        return new ListPostsHandler(
            $container->get(MapperInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(RouterInterface::class)
        );
    }
}
