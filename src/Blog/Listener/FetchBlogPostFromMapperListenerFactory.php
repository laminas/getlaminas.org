<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Listener;

use GetLaminas\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class FetchBlogPostFromMapperListenerFactory
{
    public function __invoke(ContainerInterface $container): FetchBlogPostFromMapperListener
    {
        $mapper = $container->get(MapperInterface::class);
        assert($mapper instanceof MapperInterface);

        return new FetchBlogPostFromMapperListener($mapper);
    }
}
