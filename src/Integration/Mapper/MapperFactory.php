<?php

declare(strict_types=1);

namespace GetLaminas\Integration\Mapper;

use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;
use function is_array;

final class MapperFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PdoMapper
    {
        $config = $container->get('config-packages') ?? [];
        assert(is_array($config));

        $pdo = new PDO($config['db'] ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return new PdoMapper($pdo);
    }
}
