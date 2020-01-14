<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace GetLaminas\Blog\Console;

use GetLaminas\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;

class FeedGeneratorFactory
{
    public function __invoke(ContainerInterface $container) : FeedGenerator
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
