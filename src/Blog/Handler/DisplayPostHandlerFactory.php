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
        $dispatcher = $container->get(EventDispatcherInterface::class);
        assert($dispatcher instanceof EventDispatcherInterface);

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        $notFoundHandler = $container->get(NotFoundHandler::class);
        assert($notFoundHandler instanceof NotFoundHandler);

        return new DisplayPostHandler($dispatcher, $renderer, $notFoundHandler);
    }
}
