<?php

declare(strict_types=1);

return [
    'release-feed' => [
        'verification_token' => getenv('RELEASE_FEED_TOKEN'),
    ],
    'blog'         => [
        'db' => 'sqlite:' . realpath(getcwd()) . '/var/blog/posts.db',
    ],
];
