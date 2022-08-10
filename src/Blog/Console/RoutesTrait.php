<?php

declare(strict_types=1);

namespace GetLaminas\Blog\Console;

use Mezzio\Router\Route;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

trait RoutesTrait
{
    /** @var array<string, string> */
    private array $routes = [
        'blog'               => '/blog[/]',
        'blog.post'          => '/blog/{id:[^/]+}.html',
        'blog.feed.php'      => '/blog/tag/{tag:php}.xml',
        'blog.feed.php.also' => '/blog/{tag:php}.xml',
        'blog.tag.feed'      => '/blog/tag/{tag:[^/]+}/{type:atom|rss}.xml',
        'blog.tag'           => '/blog/tag/{tag:[^/]+}',
        'blog.feed'          => '/blog/{type:atom|rss}.xml',
        'contact'            => '/contact[/]',
        'home'               => '/',
        'resume'             => '/resume',
    ];

    private function seedRoutes(RouterInterface $router): void
    {
        $middleware = new class implements MiddlewareInterface {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                throw RuntimeException('This should never be called');
            }
        };

        foreach ($this->routes as $name => $path) {
            $router->addRoute(new Route($path, $middleware, ['GET'], $name));
        }
    }
}
