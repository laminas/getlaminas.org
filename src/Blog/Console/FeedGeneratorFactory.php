<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Console;

use GetLaminas\Blog\Mapper\MapperInterface;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function getcwd;
use function realpath;

class FeedGeneratorFactory
{
    public function __invoke(ContainerInterface $container): FeedGenerator
    {
        $mapper = $container->get(MapperInterface::class);
        assert($mapper instanceof MapperInterface);

        $router = $container->get(RouterInterface::class);
        assert($router instanceof RouterInterface);

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        $serverUrlHelper = $container->get(ServerUrlHelper::class);
        assert($serverUrlHelper instanceof ServerUrlHelper);

        return new FeedGenerator(
            $mapper,
            $router,
            $renderer,
            $serverUrlHelper,
            realpath(getcwd()) . '/data/blog/authors/'
        );
    }
}
