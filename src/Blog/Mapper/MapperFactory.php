<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Mapper;

use PDO;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;

final class MapperFactory
{
    public function __invoke(ContainerInterface $container): PdoMapper
    {
        $config = $container->get('config-blog') ?? [];
        assert(is_array($config));

        $pdo = new PDO($config['db'] ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return new PdoMapper($pdo);
    }
}
