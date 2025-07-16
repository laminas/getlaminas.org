<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem\Handler;

use GetLaminas\Ecosystem\Mapper\MapperInterface;
use GetLaminas\Ecosystem\Mapper\PdoMapper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class EcosystemHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): EcosystemHandler
    {
        $mapper = $container->get(PdoMapper::class);
        assert($mapper instanceof MapperInterface);

        $renderer = $container->get(TemplateRendererInterface::class);
        assert($renderer instanceof TemplateRendererInterface);

        return new EcosystemHandler($mapper, $renderer);
    }
}
