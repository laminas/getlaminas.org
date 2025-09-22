<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Integration\Mapper;

use GetLaminas\Integration\Mapper\MapperFactory;
use GetLaminas\Integration\Mapper\PdoMapper;
use LaminasTest\Unit\Integration\CommonTestCase;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class MapperFactoryTest extends CommonTestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillInstantiatePdoMapper(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('get')->willReturn([
            'db' => 'sqlite:' . $this->testDb
        ]);

        $this->assertInstanceOf(PdoMapper::class, (new MapperFactory())($container));
    }
}
