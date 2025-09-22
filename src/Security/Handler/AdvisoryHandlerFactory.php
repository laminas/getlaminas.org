<?php

declare(strict_types=1);

namespace GetLaminas\Security\Handler;

use GetLaminas\Security\Advisory;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class AdvisoryHandlerFactory
{
    public function __invoke(ContainerInterface $container): AdvisoryHandler
    {
        $advisory = $container->get(Advisory::class);
        assert($advisory instanceof Advisory);

        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new AdvisoryHandler($advisory, $template);
    }
}
