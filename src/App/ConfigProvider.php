<?php

declare(strict_types=1);

namespace App;

use League\Plates\Engine as PlatesEngine;
use Psr\Log\LoggerInterface;
use Zend\Expressive\Plates\PlatesEngineFactory;
use Zend\Stratigility\Middleware\ErrorHandler;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
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
    public function __invoke() : array
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
    public function getDependencies() : array
    {
        return [
            'delegators' => [
                ErrorHandler::class => [
                    LoggingErrorListenerDelegator::class,
                ],
                PlatesEngine::class => [
                    Template\InjectAssetRevisionsDelegator::class,
                ],
            ],
            'factories'  => [
                Handler\AboutJoinHandler::class         => Handler\AboutJoinHandlerFactory::class,
                Handler\AboutJoinThankYouHandler::class => Handler\AboutJoinThankYouHandlerFactory::class,
                Handler\HomePageHandler::class          => Handler\HomePageHandlerFactory::class,
                LoggerInterface::class                  => AccessLoggerFactory::class,
                PlatesEngine::class                     => PlatesEngineFactory::class,
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
                'about'  => ['templates/about'],
                'app'    => ['templates/app'],
                'error'  => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }
}
