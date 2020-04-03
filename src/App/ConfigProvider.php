<?php

declare(strict_types=1);

namespace App;

use League\Plates\Engine as PlatesEngine;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Mezzio\Application;
use Mezzio\Plates\PlatesEngineFactory;
use Laminas\Stratigility\Middleware\ErrorHandler;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     */
    public function __invoke(): array
    {
        return [
            'asset-revisions' => [],
            'dependencies'    => $this->getDependencies(),
            'templates'       => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
                ListenerProviderInterface::class => AttachableListenerProvider::class,
            ],
            'delegators' => [
                ErrorHandler::class => [
                    LoggingErrorListenerDelegator::class,
                ],
                PlatesEngine::class => [
                    Template\InjectAssetRevisionsDelegator::class,
                ],
            ],
            'factories'  => [
                EventDispatcherInterface::class  => EventDispatcherFactory::class,
                Handler\StaticPageHandler::class => Handler\StaticPageHandlerFactory::class,
                LoggerInterface::class           => AccessLoggerFactory::class,
                PlatesEngine::class              => PlatesEngineFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'about'     => ['templates/about'],
                'app'       => ['templates/app'],
                'community' => ['templates/community'],
                'error'     => ['templates/error'],
                'layout'    => ['templates/layout'],
            ],
        ];
    }

    public function registerRoutes(Application $app, string $basePath = '/'): void
    {
        $basePath = rtrim($basePath, '/') . '/';
        $app->get($basePath, Handler\StaticPageHandler::class, 'app.home-page');
        $app->get($basePath . 'about[/]', Handler\StaticPageHandler::class, 'about.overview');
        $app->get($basePath . 'about/foundation', Handler\StaticPageHandler::class, 'about.foundation');
        $app->get($basePath . 'about/join', Handler\StaticPageHandler::class, 'about.join');
        $app->get($basePath . 'about/join/thank-you', Handler\StaticPageHandler::class, 'about.join-thank-you');
        $app->post($basePath . 'about/join/thank-you', Handler\StaticPageHandler::class, 'about.join-process');
        $app->get($basePath . 'participate[/]', Handler\StaticPageHandler::class, 'community.participate');
    }
}
