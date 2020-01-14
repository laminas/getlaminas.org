<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use GetLaminas\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;
use Mezzio\Helper\UrlHelper;

class SearchHandlerFactory
{
    public function __invoke(ContainerInterface $container) : SearchHandler
    {
        return new SearchHandler(
            $container->get(MapperInterface::class),
            $container->get(UrlHelper::class)
        );
    }
}
