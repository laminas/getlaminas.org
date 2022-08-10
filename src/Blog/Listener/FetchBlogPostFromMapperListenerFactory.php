<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Listener;

use GetLaminas\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

class FetchBlogPostFromMapperListenerFactory
{
    public function __invoke(ContainerInterface $container): FetchBlogPostFromMapperListener
    {
        return new FetchBlogPostFromMapperListener(
            $container->get(MapperInterface::class)
        );
    }
}
