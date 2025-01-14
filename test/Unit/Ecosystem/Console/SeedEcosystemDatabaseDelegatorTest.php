<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Ecosystem\Console;

use GetLaminas\Ecosystem\Console\SeedEcosystemDatabase;
use GetLaminas\Ecosystem\Console\SeedEcosystemDatabaseDelegator;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionProperty;

class SeedEcosystemDatabaseDelegatorTest extends TestCase
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

        $instance = new SeedEcosystemDatabase();

        $command = (new SeedEcosystemDatabaseDelegator())(
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
