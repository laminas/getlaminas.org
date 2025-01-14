<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Ecosystem\Console;

use GetLaminas\Ecosystem\Console\CreateEcosystemDatabase;
use GetLaminas\Ecosystem\Console\CreateEcosystemDatabaseDelegator;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use ReflectionProperty;

class CreateEcosystemDatabaseDelegatorTest extends TestCase
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

        $instance = new CreateEcosystemDatabase();

        $command = (new CreateEcosystemDatabaseDelegator())(
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
