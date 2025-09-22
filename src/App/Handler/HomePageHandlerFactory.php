<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;

final class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): HomePageHandler
    {
        $commercialVendors = $container->get('config')['commercial-vendors'] ?? [];
        assert(is_array($commercialVendors));

        $projects = $container->get('config')['projects-using-components'] ?? [];
        assert(is_array($projects));

        $sponsors = $container->get('config')['sponsors'] ?? [];
        assert(is_array($sponsors));

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        return new HomePageHandler($commercialVendors, $sponsors, $projects, $renderer);
    }
}
