<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Ecosystem\Handler;

use GetLaminas\Ecosystem\Handler\EcosystemHandler;
use GetLaminas\Ecosystem\Handler\EcosystemHandlerFactory;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class EcosystemHandlerFactoryTest extends TestCase
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testWillInstantiateEcosystemHandler(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $pdoMapper = $this->createMock(PdoMapper::class);
        $renderer  = $this->createMock(TemplateRendererInterface::class);

        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls($pdoMapper, $renderer);

        $this->assertInstanceOf(EcosystemHandler::class, (new EcosystemHandlerFactory())($container));
    }
}
