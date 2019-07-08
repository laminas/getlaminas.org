<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container) : HomePageHandler
    {
        return new HomePageHandler($container->get(TemplateRendererInterface::class));
    }
}
