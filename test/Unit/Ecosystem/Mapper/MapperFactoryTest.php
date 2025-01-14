<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Ecosystem\Mapper;

use GetLaminas\Ecosystem\Mapper\MapperFactory;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use LaminasTest\Unit\Ecosystem\CommonTestCase;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class MapperFactoryTest extends CommonTestCase
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
