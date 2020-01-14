<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace GetLaminas\Blog\Console;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Router\Route;
use Mezzio\Router\RouterInterface;

trait RoutesTrait
{
    private $routes = [
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

    private function seedRoutes(RouterInterface $router) : RouterInterface
    {
        $middleware = new class implements MiddlewareInterface {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ) : ResponseInterface {
            }
        };

        foreach ($this->routes as $name => $path) {
            $router->addRoute(new Route($path, $middleware, ['GET'], $name));
        }

        return $router;
    }
}
