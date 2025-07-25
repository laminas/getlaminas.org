<?php

declare(strict_types=1);

namespace LaminasTest\Unit\Integration\Handler;

use GetLaminas\Integration\Handler\IntegrationHandler;
use GetLaminas\Integration\Handler\IntegrationHandlerFactory;
use GetLaminas\Integration\Mapper\PdoMapper;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class IntegrationHandlerFactoryTest extends TestCase
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testWillInstantiateIntegrationHandler(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $pdoMapper = $this->createMock(PdoMapper::class);
        $renderer  = $this->createMock(TemplateRendererInterface::class);

        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls($pdoMapper, $renderer);

        $this->assertInstanceOf(IntegrationHandler::class, (new IntegrationHandlerFactory())($container));
    }
}
