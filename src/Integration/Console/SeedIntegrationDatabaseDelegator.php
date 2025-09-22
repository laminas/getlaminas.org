<?php

declare(strict_types=1);

namespace GetLaminas\Integration\Console;

use GetLaminas\Integration\Console\SeedIntegrationDatabase;
use GetLaminas\Integration\Mapper\PdoMapper;
use Psr\Container\ContainerInterface;

use function assert;

final class SeedIntegrationDatabaseDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): SeedIntegrationDatabase {
        /** @var SeedIntegrationDatabase $command */
        $command = $factory();

        $pdoMapper = $container->get(PdoMapper::class);
        assert($pdoMapper instanceof PdoMapper);

        $command->setMapper($pdoMapper);
        return $command;
    }
}
