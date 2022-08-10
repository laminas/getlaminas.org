<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class StaticPageHandlerFactory
{
    public function __invoke(ContainerInterface $container): StaticPageHandler
    {
        return new StaticPageHandler($container->get(TemplateRendererInterface::class));
    }
}
