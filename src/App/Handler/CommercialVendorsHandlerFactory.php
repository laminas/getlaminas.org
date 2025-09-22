<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;

final class CommercialVendorsHandlerFactory
{
    public function __invoke(ContainerInterface $container): CommercialVendorsHandler
    {
        $commercialVendors = $container->get('config')['commercial-vendors'] ?? [];
        assert(is_array($commercialVendors));

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        return new CommercialVendorsHandler($commercialVendors, $renderer);
    }
}
