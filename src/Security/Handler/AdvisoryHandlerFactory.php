<?php

declare(strict_types=1);

namespace GetLaminas\Security\Handler;

use GetLaminas\Security\Advisory;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class AdvisoryHandlerFactory
{
    public function __invoke(ContainerInterface $container): AdvisoryHandler
    {
        $advisory = $container->get(Advisory::class);
        $template = $container->get(TemplateRendererInterface::class);

        return new AdvisoryHandler($advisory, $template);
    }
}
