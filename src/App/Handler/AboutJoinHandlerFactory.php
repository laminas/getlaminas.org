<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AboutJoinHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AboutJoinHandler
    {
        return new AboutJoinHandler($container->get(TemplateRendererInterface::class));
    }
}
