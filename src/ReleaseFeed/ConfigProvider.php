<?php

namespace GetLaminas\ReleaseFeed;

use Mezzio\Application;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'release-feed' => [
                'feed-file'          => getcwd() . '/data/cache/releases.rss',
                'verification_token' => '',
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                DisplayFeedHandler::class     => DisplayFeedHandlerFactory::class,
                ReceiveFeedItemHandler::class => ReceiveFeedItemHandlerFactory::class,
                VerifyTokenMiddleware::class  => VerifyTokenMiddlewareFactory::class,
            ],
        ];
    }

    public function registerRoutes(Application $app): void
    {
        $app->get('/releases/rss.xml', DisplayFeedHandler::class, 'releases.feed');
        $app->post('/api/release', [
            VerifyTokenMiddleware::class,
            BodyParamsMiddleware::class,
            ReceiveFeedItemHandler::class
        ], 'api.release');
    }
}
