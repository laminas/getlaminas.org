<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Integration\Console;

use GetLaminas\Integration\Console\SeedIntegrationDatabase;
use GetLaminas\Integration\Console\SeedIntegrationDatabaseDelegator;
use GetLaminas\Integration\Mapper\PdoMapper;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionProperty;

final class SeedIntegrationsDatabaseDelegatorTest extends TestCase
{
    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testCanAttachMapperToCommand(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $pdoMapper = $this->createMock(PdoMapper::class);

        $container->expects($this->once())
            ->method('get')
            ->willReturn($pdoMapper);

        $instance = new SeedIntegrationDatabase();

        $command = (new SeedIntegrationDatabaseDelegator())(
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
