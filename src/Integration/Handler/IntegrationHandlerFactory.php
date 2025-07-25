<?php

declare(strict_types=1);

namespace GetLaminas\Integration\Handler;

use GetLaminas\Integration\Mapper\MapperInterface;
use GetLaminas\Integration\Mapper\PdoMapper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class IntegrationHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): IntegrationHandler
    {
        $mapper = $container->get(PdoMapper::class);
        assert($mapper instanceof MapperInterface);

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        return new IntegrationHandler($mapper, $renderer);
    }
}
