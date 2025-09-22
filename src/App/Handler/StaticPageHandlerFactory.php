<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class StaticPageHandlerFactory
{
    public function __invoke(ContainerInterface $container): StaticPageHandler
    {
        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        return new StaticPageHandler($renderer);
    }
}
