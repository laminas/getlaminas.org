---
id: 2025-02-13-creating-pages-and-modules
author: bidi
title: 'Creating Pages and Modules'
draft: false
public: true
created: '2025-02-13T11:00:00-01:00'
updated: '2025-02-13T11:00:00-01:00'
tags:
    - mezzio
    - skeleton
    - tutorial
    - content-management
    - cli
---

In the previous article we used the Mezzio Skeleton Installer to build a very basic application.
Let's explore what we can do with it right away.

The current components allow us to build a presentation site with static pages.
It may not be much to speak of yet, but we can already create new pages and organize them in multiple modules.

### Creating a new page

From the beginning, the Mezzio Skeleton Installer creates a home page that lists the components we chose during installation.
Feel free to edit that page however you see fit.
Don't worry about any dynamic twig elements for now.

What if you want a new page, like an 'About Us' page?
Let's go through the steps of creating a new page.
All of these steps are required, so it doesn't really matter what order we do them in.

#### The new handler

Let's create the `App\Handler\AboutPageHandler.php` file.
So far we have a single module named `App`, so the new Handler goes into the `src/App/src/Handler` folder.
Go ahead and create the file `src/App/src/Handler/AboutPageHandler.php` with this content:

```php
<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AboutPageHandler implements RequestHandlerInterface
{
    public function __construct(
        private string $containerName,
        private RouterInterface $router,
        private ?TemplateRendererInterface $template = null
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [];
        return new HtmlResponse($this->template->render('app::about-page', $data));
    }
}
```

#### The new template

On the last line above there is a template file `app::about-page`.
If you check `src/App/src/ConfigProvider.php`, you can see what that string means:

- `app` defines a folder in which the template file is kept, in this case `__DIR__ . '/../templates/app'` which resolves to `src/App/templates/app`.
- `about-page` is the file name without the extensions.

Let's create the About page template named `about-page.html.twig` with a few lines of content for when we check the results of your work:

```twig
{% extends '@layout/default.html.twig' %}

{% block title %}About Us{% endblock %}

{% block content %}
    <div class="jumbotron">
        <h1>About Us</h1>
        <p>This is some great content!</p>
    </div>
{% endblock %}
```

#### The factory for the new handler

The new handler requires a Factory.
Let's create the `src/App/src/Handler/AboutPageHandlerFactory.php` file.
Its content is very similar to the `HomePageHandlerFactory.php` file, but returns the `AboutPageHandler` class instead.

```php
<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

class AboutPageHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        $router = $container->get(RouterInterface::class);
        assert($router instanceof RouterInterface);

        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;
        assert($template instanceof TemplateRendererInterface || null === $template);

        return new AboutPageHandler($container::class, $router, $template);
    }
}
```

#### The ConfigProvider file

The factory needs to be added in the `src/App/src/ConfigProvider.php` file.
The location to add the new entry is in the `getDependencies` method, under the `factories` key, like we did below.
You can replace the current `getDependencies` method with this one:

```php
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Handler\PingHandler::class => Handler\PingHandler::class,
            ],
            'factories'  => [
                Handler\HomePageHandler::class  => Handler\HomePageHandlerFactory::class,
                Handler\AboutPageHandler::class => Handler\AboutPageHandlerFactory::class,
            ],
        ];
    }
```

#### The routing

Finally, add the new handler to the `config/routes.php` file.
On the bottom of the file we already have 2 entries created by the Mezzio Skeleton Installer.
You can check the Handler names to tell what each of them is for.

For the new page, we need to append the line for the `AboutPageHandler` to that list, like we have done below.
Feel free to replace the return statement with this one:

```php
return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/', App\Handler\HomePageHandler::class, 'home');
    $app->get('/api/ping', App\Handler\PingHandler::class, 'api.ping');
    $app->get('/about', App\Handler\AboutPageHandler::class, 'about');
};
```

#### The result

You are done!
Next make sure to have your web server started by running this command in your project folder `php -S 0.0.0.0:8080 -t public`.
To visit your new page, type this url in the browser `http://localhost:8080/about`.

### Creating a new module

If you want to organize your pages in several modules, Mezzio provides an easy method to create the folder structure.

Run this command to create and register the module named `Page`.
Obviously, you can use whatever name is relevant for your module.

```shell
composer mezzio mezzio:module:create Page
```

The command will perform the following actions:

- Create the folders `src/Page/src` and `src/Page/templates`.
- Create the file `src/Page/src/ConfigProvider.php` with a basic configuration.
- Add the above ConfigProvider in the aggregator in `config/config.php`.
- Register the module in `composer.json`, under the `autoload` key.

The next steps involve creating handlers, factories and templates like we explored in the first part of this article.

### Additional Resources

Learn more about [Command Line Tooling](https://docs.mezzio.dev/mezzio/v3/reference/cli-tooling/)
