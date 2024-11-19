<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Console;

use GetLaminas\Ecosystem\Mapper\PdoMapper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class CreateEcosystemDatabaseDelegator
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): CreateEcosystemDatabase {
        /** @var CreateEcosystemDatabase $command */
        $command = $factory();

        $pdoMapper = $container->get(PdoMapper::class);
        assert($pdoMapper instanceof PdoMapper);

        $command->setMapper($pdoMapper);
        return $command;
    }
}
