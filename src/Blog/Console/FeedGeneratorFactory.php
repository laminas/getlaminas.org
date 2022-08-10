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
        return new FeedGenerator(
            $container->get(MapperInterface::class),
            $container->get(RouterInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(ServerUrlHelper::class),
            realpath(getcwd()) . '/data/blog/authors/'
        );
    }
}
