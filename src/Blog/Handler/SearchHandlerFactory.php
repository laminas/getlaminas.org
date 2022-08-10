<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use GetLaminas\Blog\Mapper\MapperInterface;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

class SearchHandlerFactory
{
    public function __invoke(ContainerInterface $container): SearchHandler
    {
        return new SearchHandler(
            $container->get(MapperInterface::class),
            $container->get(UrlHelper::class)
        );
    }
}
