<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AboutJoinThankYouHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AboutJoinThankYouHandler
    {
        return new AboutJoinThankYouHandler($container->get(TemplateRendererInterface::class));
    }
}
