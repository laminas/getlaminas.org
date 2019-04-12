<?php

use Zend\Expressive\Swoole\StaticResourceHandler\ContentTypeFilterMiddleware;

return [
    'zend-expressive-swoole' => [
        'enable_coroutine' => true,
        'swoole-http-server' => [
            'host' => '0.0.0.0',
            'port' => 9000,
            'mode' => SWOOLE_PROCESS,
            'process-name' => 'getlaminas',
            'static-files' => [
                'type-map' => array_merge(ContentTypeFilterMiddleware::TYPE_MAP_DEFAULT, [
                    'asc'         => 'application/octet-stream',
                    'webmanifest' => 'application/manifest+json',
                ]),
                'gzip' => [
                    'level' => 6,
                ],
                'directives' => [
                    '/\.(?:ico|png|gif|jpg|jpeg|svg)$/' => [
                        'cache-control' => ['public', 'max-age=' . 60 * 60 * 24 * 365],
                        'last-modified' => true,
                        'etag' => true,
                    ],
                    '/\.(?:asc)$/' => [
                        'cache-control' => ['public', 'max-age=' . 60 * 60 * 24 * 365],
                        'last-modified' => true,
                    ],
                    '/\.(?:css|js|xml|webmanifest)$/' => [
                        'cache-control' => ['public', 'max-age=' . 60 * 60 * 24 * 30],
                        'last-modified' => true,
                        'etag' => true,
                    ],
                ],
            ],
        ],
    ],
];
