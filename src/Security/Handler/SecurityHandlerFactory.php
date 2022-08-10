<?php

declare(strict_types=1);

namespace GetLaminas\Security\Handler;

use GetLaminas\Security\Advisory;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class SecurityHandlerFactory
{
    public function __invoke(ContainerInterface $container): SecurityHandler
    {
        $advisory = $container->get(Advisory::class);
        $template = $container->get(TemplateRendererInterface::class);

        return new SecurityHandler($advisory, $template);
    }
}
