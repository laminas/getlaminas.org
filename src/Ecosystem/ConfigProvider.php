<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem;

use GetLaminas\Ecosystem\Console\CreateEcosystemDatabase;
use GetLaminas\Ecosystem\Console\CreateEcosystemDatabaseDelegator;
use GetLaminas\Ecosystem\Console\SeedEcosystemDatabase;
use GetLaminas\Ecosystem\Console\SeedEcosystemDatabaseDelegator;
use GetLaminas\Ecosystem\Handler\EcosystemHandler;
use GetLaminas\Ecosystem\Handler\EcosystemHandlerFactory;
use GetLaminas\Ecosystem\Mapper\MapperFactory;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use Mezzio\Application;
use Phly\ConfigFactory\ConfigFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'ecosystem'    => $this->getConfig(),
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
                'config-packages'       => ConfigFactory::class,
                EcosystemHandler::class => EcosystemHandlerFactory::class,
                PdoMapper::class        => MapperFactory::class,
            ],
            // phpcs:enable
            // @codingStandardsIgnoreEnd
            'invokables' => [
                SeedEcosystemDatabase::class   => SeedEcosystemDatabase::class,
                CreateEcosystemDatabase::class => CreateEcosystemDatabase::class,
            ],
            'delegators' => [
                CreateEcosystemDatabase::class => [
                    CreateEcosystemDatabaseDelegator::class,
                ],
                SeedEcosystemDatabase::class   => [
                    SeedEcosystemDatabaseDelegator::class,
                ],
            ],
        ];
    }

    public function getTemplateConfig(): array
    {
        return [
            'paths' => [
                'ecosystem' => [__DIR__ . '/templates'],
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'ecosystem:seed-db'   => SeedEcosystemDatabase::class,
                'ecosystem:create-db' => CreateEcosystemDatabase::class,
            ],
        ];
    }

    public function registerRoutes(Application $app, string $basePath = '/ecosystem'): void
    {
        $app->get($basePath . '[/]', EcosystemHandler::class, 'app.ecosystem');
    }
}
