<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Integration\Console;

use GetLaminas\Integration\Console\CreateIntegrationDatabase;
use GetLaminas\Integration\Console\CreateIntegrationDatabaseDelegator;
use GetLaminas\Integration\Mapper\PdoMapper;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use ReflectionProperty;

final class CreateIntegrationDatabaseDelegatorTest extends TestCase
{
    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testWillAttachMapperToCommand(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $pdoMapper = $this->createMock(PdoMapper::class);

        $container->expects($this->once())
            ->method('get')
            ->willReturn($pdoMapper);

        $instance = new CreateIntegrationDatabase();

        $command = (new CreateIntegrationDatabaseDelegator())(
            $container,
            '',
            function () use ($instance) {
                return $instance;
            }
        );

         $reflectionMapper = (new ReflectionProperty($command, 'mapper'))->getValue($command);

         $this->assertInstanceOf(PdoMapper::class, $reflectionMapper);
    }
}
