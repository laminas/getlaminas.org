<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace GetLaminas\Blog\Handler;

use GetLaminas\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;

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
