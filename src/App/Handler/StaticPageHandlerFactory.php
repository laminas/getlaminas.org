<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;
use Mezzio\Template\TemplateRendererInterface;

class StaticPageHandlerFactory
{
    public function __invoke(ContainerInterface $container): StaticPageHandler
    {
        return new StaticPageHandler($container->get(TemplateRendererInterface::class));
    }
}
