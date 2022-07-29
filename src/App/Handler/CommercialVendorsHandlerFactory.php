<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class CommercialVendorsHandlerFactory
{
    public function __invoke(ContainerInterface $container): CommercialVendorsHandler
    {
        return new CommercialVendorsHandler(
            $container->get('config')['commercial-vendors'],
            $container->get(TemplateRendererInterface::class)
        );
    }
}
