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
        $mapper = $container->get(MapperInterface::class);
        assert($mapper instanceof MapperInterface);

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        $router = $container->get(RouterInterface::class);
        assert($router instanceof RouterInterface);

        return new ListPostsHandler($mapper, $renderer, $router);
    }
}
