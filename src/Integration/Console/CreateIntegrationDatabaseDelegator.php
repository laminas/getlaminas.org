<?php

declare(strict_types=1);

namespace GetLaminas\Integration\Console;

use GetLaminas\Integration\Mapper\PdoMapper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class CreateIntegrationDatabaseDelegator
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): CreateIntegrationDatabase {
        /** @var CreateIntegrationDatabase $command */
        $command = $factory();

        $pdoMapper = $container->get(PdoMapper::class);
        assert($pdoMapper instanceof PdoMapper);

        $command->setMapper($pdoMapper);
        return $command;
    }
}
