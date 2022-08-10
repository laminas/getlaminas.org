<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): HomePageHandler
    {
        $commercialVendors = $container->get('config')['commercial-vendors'] ?? [];
        assert(is_array($commercialVendors));

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        return new HomePageHandler($commercialVendors, $renderer);
    }
}
