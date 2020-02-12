<?php

namespace GetLaminas\ReleaseFeed;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Mezzio\Application;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'release-feed' => [
                'feed-file'          => getcwd() . '/data/cache/releases.rss',
                'verification_token' => '',
            ],
        ];
    }

    public function getDependencies() : array
    {
        return [
            'aliases' => [
                ResponseFactoryInterface::class => ResponseFactory::class,
                StreamFactoryInterface::class   => StreamFactory::class,
            ],
            'factories' => [
                DisplayFeedHandler::class     => DisplayFeedHandlerFactory::class,
                ReceiveFeedItemHandler::class => ReceiveFeedItemHandlerFactory::class,
                ResponseFactory::class        => InvokableFactory::class,
                StreamFactory::class          => InvokableFactory::class,
                VerifyTokenMiddleware::class  => VerifyTokenMiddlewareFactory::class,
            ],
        ];
    }

    public function registerRoutes(Application $app) : void
    {
        $app->get('/releases/rss.xml', DisplayFeedHandler::class, 'releases.feed');
        $app->post('/api/release', [
            VerifyTokenMiddleware::class,
            BodyParamsMiddleware::class,
            ReceiveFeedItemHandler::class
        ], 'api.release');
    }
}
