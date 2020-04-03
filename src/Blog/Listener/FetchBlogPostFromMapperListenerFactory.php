<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

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
