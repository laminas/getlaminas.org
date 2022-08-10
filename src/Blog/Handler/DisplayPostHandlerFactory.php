<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Handler;

use Mezzio\Handler\NotFoundHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class DisplayPostHandlerFactory
{
    public function __invoke(ContainerInterface $container): DisplayPostHandler
    {
        return new DisplayPostHandler(
            $container->get(EventDispatcherInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(NotFoundHandler::class)
        );
    }
}
