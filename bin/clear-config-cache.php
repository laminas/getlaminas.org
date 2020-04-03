<?php
/**
 * Script for clearing the configuration cache.
 *
 * Can also be invoked as `composer clear-config-cache`.
 *
 * @see       https://github.com/laminas/getlaminas.org for the canonical source repository
 * @copyright https://github.com/laminas/getlaminas.org/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/getlaminas.org/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

chdir(__DIR__ . '/../');

require 'vendor/autoload.php';

$config = include 'config/config.php';

if (! isset($config['config_cache_path'])) {
    echo "No configuration cache path found" . PHP_EOL;
    exit(0);
}

if (! file_exists($config['config_cache_path'])) {
    printf(
        "Configured config cache file '%s' not found%s",
        $config['config_cache_path'],
        PHP_EOL
    );
    exit(0);
}

if (false === unlink($config['config_cache_path'])) {
    printf(
        "Error removing config cache file '%s'%s",
        $config['config_cache_path'],
        PHP_EOL
    );
    exit(1);
}

printf(
    "Removed configured config cache file '%s'%s",
    $config['config_cache_path'],
    PHP_EOL
);
exit(0);
