<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace GetLaminas\Blog\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zend\Expressive\Handler\NotFoundHandler;
use Zend\Expressive\Template\TemplateRendererInterface;

class DisplayPostHandlerFactory
{
    public function __invoke(ContainerInterface $container) : DisplayPostHandler
    {
        return new DisplayPostHandler(
            $container->get(EventDispatcherInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(NotFoundHandler::class)
        );
    }
}
