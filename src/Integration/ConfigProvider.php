<?php

declare(strict_types=1);

namespace GetLaminas\Integration;

use GetLaminas\Integration\Console\CreateIntegrationDatabase;
use GetLaminas\Integration\Console\CreateIntegrationDatabaseDelegator;
use GetLaminas\Integration\Console\SeedIntegrationDatabase;
use GetLaminas\Integration\Console\SeedIntegrationDatabaseDelegator;
use GetLaminas\Integration\Handler\IntegrationHandler;
use GetLaminas\Integration\Handler\IntegrationHandlerFactory;
use GetLaminas\Integration\Mapper\MapperFactory;
use GetLaminas\Integration\Mapper\PdoMapper;
use Mezzio\Application;
use Phly\ConfigFactory\ConfigFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'integration'  => $this->getConfig(),
            'dependencies' => $this->getDependencies(),
            'laminas-cli'  => $this->getConsoleConfig(),
            'templates'    => $this->getTemplateConfig(),
        ];
    }

    public function getConfig(): array
    {
        return [
            'db' => null,
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                'config-packages'         => ConfigFactory::class,
                IntegrationHandler::class => IntegrationHandlerFactory::class,
                PdoMapper::class          => MapperFactory::class,
            ],
            // phpcs:enable
            // @codingStandardsIgnoreEnd
            'invokables' => [
                SeedIntegrationDatabase::class   => SeedIntegrationDatabase::class,
                CreateIntegrationDatabase::class => CreateIntegrationDatabase::class,
            ],
            'delegators' => [
                CreateIntegrationDatabase::class => [
                    CreateIntegrationDatabaseDelegator::class,
                ],
                SeedIntegrationDatabase::class   => [
                    SeedIntegrationDatabaseDelegator::class,
                ],
            ],
        ];
    }

    public function getTemplateConfig(): array
    {
        return [
            'paths' => [
                'integration' => [__DIR__ . '/templates'],
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'integration:seed-db'   => SeedIntegrationDatabase::class,
                'integration:create-db' => CreateIntegrationDatabase::class,
            ],
        ];
    }

    public function registerRoutes(Application $app, string $basePath = '/integrations'): void
    {
        $app->get($basePath . '[/]', IntegrationHandler::class, 'app.integrations');
    }
}
