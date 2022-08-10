<?php

declare(strict_types=1);

namespace GetLaminas\Blog;

use League\Plates\Engine;
use Mezzio\Application;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'blog'         => $this->getConfig(),
            'dependencies' => $this->getDependencies(),
            'laminas-cli'  => $this->getConsoleConfig(),
            'templates'    => $this->getTemplateConfig(),
        ];
    }

    public function getConfig(): array
    {
        return [
            'db' => null,
        ];
    }

    public function getDependencies(): array
    {
        return [
            // @codingStandardsIgnoreStart
            // phpcs:disable
            'factories' => [
                'config-blog'                                   => ConfigFactory::class,
                Console\FeedGenerator::class                    => Console\FeedGeneratorFactory::class,
                Handler\DisplayPostHandler::class               => Handler\DisplayPostHandlerFactory::class,
                Handler\FeedHandler::class                      => Handler\FeedHandlerFactory::class,
                Handler\ListPostsHandler::class                 => Handler\ListPostsHandlerFactory::class,
                Handler\SearchHandler::class                    => Handler\SearchHandlerFactory::class,
                Listener\FetchBlogPostFromMapperListener::class => Listener\FetchBlogPostFromMapperListenerFactory::class,
                Mapper\MapperInterface::class                   => Mapper\MapperFactory::class,
            ],
            // phpcs:enable
            // @codingStandardsIgnoreEnd
            'invokables' => [
                Console\GenerateSearchData::class => Console\GenerateSearchData::class,
                Console\SeedBlogDatabase::class   => Console\SeedBlogDatabase::class,
            ],
            'delegators' => [
                AttachableListenerProvider::class => [
                    Listener\FetchBlogPostEventListenersDelegator::class,
                ],
                Engine::class                     => [
                    PlatesFunctionsDelegator::class,
                ],
            ],
        ];
    }

    public function getTemplateConfig(): array
    {
        return [
            'paths' => [
                'blog' => [__DIR__ . '/templates'],
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'blog:feed-generator'       => Console\FeedGenerator::class,
                'blog:generate-search-data' => Console\GenerateSearchData::class,
                'blog:seed-db'              => Console\SeedBlogDatabase::class,
            ],
        ];
    }

    public function registerRoutes(Application $app, string $basePath = '/blog'): void
    {
        $app->get($basePath . '[/]', Handler\ListPostsHandler::class, 'blog');
        $app->get($basePath . '/{id:[^/]+}.html', Handler\DisplayPostHandler::class, 'blog.post');
        $app->get($basePath . '/tag/{tag:php}.xml', Handler\FeedHandler::class, 'blog.feed.php');
        $app->get($basePath . '/{tag:php}.xml', Handler\FeedHandler::class, 'blog.feed.php.also');
        $app->get($basePath . '/tag/{tag:[^/]+}/{type:atom|rss}.xml', Handler\FeedHandler::class, 'blog.tag.feed');
        $app->get($basePath . '/tag/{tag:[^/]+}', Handler\ListPostsHandler::class, 'blog.tag');
        $app->get($basePath . '/{type:atom|rss}.xml', Handler\FeedHandler::class, 'blog.feed');
        $app->get($basePath . '/search[/]', Handler\SearchHandler::class, 'blog.search');
    }
}
