<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Console;

use GetLaminas\Ecosystem\Mapper\PdoMapper;
use Psr\Container\ContainerInterface;

use function assert;

class SeedEcosystemDatabaseDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): SeedEcosystemDatabase {
        /** @var SeedEcosystemDatabase $command */
        $command = $factory();

        $pdoMapper = $container->get(PdoMapper::class);
        assert($pdoMapper instanceof PdoMapper);

        $command->setMapper($pdoMapper);
        return $command;
    }
}
