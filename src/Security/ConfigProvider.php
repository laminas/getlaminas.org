<?php

namespace GetLaminas\Security;

use Mezzio\Application;
use Laminas\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    public function getDependencies() : array
    {
        return [
            'factories' => [
                Advisory::class                => AdvisoryFactory::class,
                Console\BuildCommand::class    => InvokableFactory::class,
                Handler\AdvisoryHandler::class => Handler\AdvisoryHandlerFactory::class,
                Handler\SecurityHandler::class => Handler\SecurityHandlerFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates() : array
    {
        return [
            'paths' => [
                'security' => ['templates/security'],
            ],
        ];
    }

    public function registerRoutes(Application $app, string $basePath = '/security') : void
    {
        $app->get($basePath . '[/]', Handler\SecurityHandler::class, 'security');
        $app->get($basePath . '/{action:feed|advisories}', Handler\SecurityHandler::class, 'security.pages');
        $app->get($basePath . '/advisory/:advisory', Handler\AdvisoryHandler::class, 'security.advisory');
    }
}
