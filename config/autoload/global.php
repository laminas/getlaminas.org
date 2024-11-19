<?php

declare(strict_types=1);

use GetLaminas\Ecosystem\Console\CreateEcosystemDatabase;

return [
    'release-feed' => [
        'verification_token' => getenv('RELEASE_FEED_TOKEN'),
    ],
    'blog'         => [
        'db' => 'sqlite:' . realpath(getcwd()) . '/var/blog/posts.db',
    ],
    'packages'     => [
        'db' => 'sqlite:' . realpath(
            getcwd()
        ) . sprintf('/%s/%s', CreateEcosystemDatabase::PACKAGES_DB_PATH, CreateEcosystemDatabase::PACKAGES_DB_FILE),
    ],
];
