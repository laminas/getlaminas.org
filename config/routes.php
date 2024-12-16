<?php // phpcs:disable PSR12.Files.FileHeader.IncorrectOrder


declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // Application routes
    (new App\ConfigProvider())->registerRoutes($app, '/');

    // Blog routes
    (new GetLaminas\Blog\ConfigProvider())->registerRoutes($app, '/blog');

    // Laminas ecosystem routes
    (new GetLaminas\Ecosystem\ConfigProvider())->registerRoutes($app);

    // Security advisory routes
    (new GetLaminas\Security\ConfigProvider())->registerRoutes($app, '/security');

    // Release API
    (new GetLaminas\ReleaseFeed\ConfigProvider())->registerRoutes($app);
};
