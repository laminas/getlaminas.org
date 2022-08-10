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
        $mapper = $container->get(MapperInterface::class);
        assert($mapper instanceof MapperInterface);

        $urlHelper = $container->get(UrlHelper::class);
        assert($urlHelper instanceof UrlHelper);

        return new SearchHandler($mapper, $urlHelper);
    }
}
