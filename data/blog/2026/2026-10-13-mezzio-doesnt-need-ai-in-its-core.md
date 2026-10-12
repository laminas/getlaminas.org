---
id: 2026-10-13-mezzio-doesnt-need-ai-in-its-core
author: bidi
title: "Mezzio Doesn't Need AI in Its Core. AI Follows Its Lead"
draft: false
public: true
created: '2026-10-13T11:00:00-01:00'
updated: '2026-10-13T11:00:00-01:00'
tags:
  - mezzio
  - ai-coding
  - psr-15
  - configuration
  - convention
  - dependency-injection
  - coding-standards
  - modular-architecture
openGraphImage: '2026-10-13-mezzio-doesnt-need-ai-in-its-core.png'
openGraphDescription: "Mezzio doesn't need AI in its core. AI reads Mezzio's code and follows its lead."
---

# Mezzio Doesn't Need AI in Its Core. AI Follows Its Lead

## Introduction

AI coding assistants have become part of everyday PHP development.
In response, many tools are adding core features to create the context and structure needed for AI to generate better code.

**Mezzio**, the PSR-15 middleware microframework from the Laminas Project, takes a different position.
It doesn't need AI in its core, because **AI reads Mezzio's code and follows its lead**.

An assistant is only as good as the examples it learns from.
In a Mezzio application, those examples are built on explicit configuration, shared conventions, and solid engineering principles: dependency injection, single responsibility, strict coding standards, and extensive tests.
When the code is clear, the assistant's output is clear too.

Mezzio is also the path forward for the Laminas ecosystem.
As **Laminas MVC** heads toward end of life, the Laminas Technical Steering Committee recommends Mezzio as its successor (see [Laminas MVC Is Retiring](https://getlaminas.org/blog/2025-06-06-laminas-mvc-is-retiring.html)).

## Configuration vs. Convention: The Core Argument

"Configuration versus convention" is usually framed as a choice.
Configuration means you state everything explicitly, and convention means sensible defaults let you write less.
Each has a familiar downside: configuration can feel verbose, and convention can hide behavior behind rules you have to memorize.

Mezzio does not pick one side.
It gives each approach the job it does best:

|                       | Decided by configuration                           | Decided by convention                                       |
|-----------------------|----------------------------------------------------|-------------------------------------------------------------|
| **Question answered** | *What* is in the application?                      | *How* is the code shaped?                                   |
| **Examples**          | Middleware pipeline, routes, registered components | PSR interfaces, `ConfigProvider` classes, factories, layout |
| **Lives in**          | Plain PHP files under `config/`                    | PHP-FIG standards and Mezzio's project structure            |
| **Benefit for devs**  | Nothing runs unless you add it                     | Every feature looks like every other feature                |
| **Benefit for AI**    | Behavior is traceable, not inferred                | New code follows an obvious pattern                         |

### Configuration Keeps Mezzio Lean

Mezzio is a microframework on purpose.
It does not ship an ORM, an authentication layer, or a templating system you didn't ask for.
A fresh application contains routing, a dependency injection container, and a middleware pipeline, and every other capability is something you choose to add.

That choice is always recorded in configuration you can read.
The middleware pipeline is a plain list in `config/pipeline.php`:

```php
// config/pipeline.php
$app->pipe(ErrorHandler::class);
$app->pipe(ServerUrlMiddleware::class);
$app->pipe(RouteMiddleware::class);
// ...
$app->pipe(DispatchMiddleware::class);
$app->pipe(NotFoundHandler::class);
```

Routes are mapped in `config/routes.php`:

```php
// config/routes.php
$app->get('/hello', App\Handler\HelloHandler::class, 'hello');
```

And every installed component is registered in `config/config.php`:

```php
// config/config.php
$aggregator = new ConfigAggregator([
    Mezzio\Hal\ConfigProvider::class,
    App\ConfigProvider::class,
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
], 'data/config-cache.php');
```

If middleware runs, it is in the pipeline.
If a path responds, it is in the routes.
If a component is active, its `ConfigProvider` is in the aggregator.
There is no hidden kernel, no auto-discovery to remember, and no feature that is active simply because it was bundled.

### Convention Keeps Mezzio Predictable

Explicit configuration makes an application readable.
Shared conventions make it consistent.

**Standards define the shape of code.**
Mezzio is built on PHP-FIG standards: **PSR-7** for HTTP messages, **PSR-11** for containers, and **PSR-15** for middleware and request handlers.
Every handler implements `RequestHandlerInterface` with a single `handle()` method:

```php
// src/App/src/Handler/HelloHandler.php
namespace App\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class HelloHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['message' => 'Hello']);
    }
}
```

Every middleware implements `MiddlewareInterface` with a single `process()` method.
Once you have seen one, you have seen the shape of all of them.
Because these are open standards rather than framework-specific APIs, the handlers and middleware an assistant writes for you stay portable and remain useful long after the prompt that produced them.

**Patterns define how code is wired.**
Each module, and each Laminas or Mezzio component, exposes its configuration through a `ConfigProvider` class, and services are built by factories registered under `dependencies`:

```php
// src/Orders/src/ConfigProvider.php
namespace Orders;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                Handler\ListOrdersHandler::class => Handler\ListOrdersHandlerFactory::class,
            ],
        ];
    }
}
```

**Structure defines where code lives.**
In the modular layout, each feature is a self-contained directory with its own code, templates, and configuration:

```text
src/
├── App/
│   ├── src/
│   │   ├── ConfigProvider.php
│   │   └── Handler/
│   └── templates/
└── Orders/
    ├── src/
    │   ├── ConfigProvider.php
    │   ├── Handler/
    │   └── Middleware/
    └── templates/
```

The same conventions apply to your code and to every component you install, so the application never mixes competing styles.

### Why AI Needs Both

**Configuration helps AI read.**
To understand a Mezzio application, an assistant doesn't need to know undocumented framework internals.
It can open `config/pipeline.php`, `config/routes.php`, and `config/config.php`, then follow class names to their handlers and factories.
Every step of the request lifecycle is a file it can see, which leaves little room for guesswork or invented behavior.

**Convention helps AI write.**
When asked to add a feature, an assistant has clear examples to imitate: a handler that implements `RequestHandlerInterface`, a factory beside it, a registration in the module's `ConfigProvider`, and a route in `config/routes.php`.
The result fits the existing file structure, architecture, and standards instead of introducing a new style with every prompt.

**Leanness helps AI focus.**
A small codebase fits more easily into an assistant's context.
Fewer unused packages mean fewer irrelevant files, fewer misleading examples, and less room for mistakes.

## Principles AI Can Follow

Configuration and convention explain how AI finds its way around a Mezzio application.
The engineering principles behind Mezzio and the Laminas components explain why what it finds is worth copying.

**Dependency injection.**
Every dependency is declared in a constructor and supplied by a factory registered in the container.
Nothing is pulled from a global or created out of sight, so an assistant never has to guess where an object comes from, and it learns to write new classes the same way.

**Single responsibility.**
A handler handles one route.
A middleware does one job in the pipeline.
A factory builds one service.
Small, focused units are easy for an assistant to understand in isolation, easy to imitate, and easy to replace without side effects.

**Strict coding standards.**
The skeleton ships with PHP_CodeSniffer configured for the Laminas coding standard, run alongside the test suite with `composer check`.
That gives AI-generated code the same quality gate as code written by hand: if it doesn't meet the standard, it doesn't pass.

**Tests as examples.**
Mezzio and the Laminas components are backed by extensive test suites, and the skeleton includes PHPUnit from the start.
For an assistant, tests do two jobs.
They are examples of how each kind of class is meant to be exercised, and they are the mechanism that verifies whatever it produces.

**Safe defaults.**
The skeleton also includes a security-advisories package that blocks dependencies with known vulnerabilities, configuration caching enabled by default, and a development mode (`composer development-enable` and `composer development-disable`) that keeps development-only modules out of production.

In effect, an AI assistant working in a Mezzio project is learning from the project itself.
The quality of the codebase sets the quality of what the assistant produces, and Mezzio sets that bar high from the first commit.

## Starting Lean: The Skeleton Installer

Every Mezzio application begins with the **Mezzio skeleton installer**:

```bash
composer create-project mezzio/mezzio-skeleton mezzio
```

Instead of deciding for you, the installer asks you to choose an application structure (minimal, flat, or modular), a PSR-11 dependency injection container (laminas-servicemanager is recommended), a router, an optional template renderer such as laminas-view, and an optional development error handler.
These answers become the application's first configuration.
For a guided walkthrough, see [using the Mezzio skeleton installer](https://getlaminas.org/blog/2025-01-30-mezzio101-using-mezzio-skeleton-installer.html) from the Mezzio 101 series.

## Growing on Demand: Laminas and Mezzio Components

Starting lean only works if adding features is easy.
Two components included in the skeleton make it so:

- **laminas-component-installer:** a Composer plugin that looks for an `extra.laminas.config-provider` entry in any package you install and, if found, offers to register that provider in `config/config.php`.
- **laminas-config-aggregator:** merges configuration from every registered `ConfigProvider` in order, with later entries taking precedence, and supports configuration caching.

Adding HAL support to an API takes one command:

```bash
composer require mezzio/mezzio-hal
```

The installer detects the package's config provider and, once you choose `config/config.php`, adds `Mezzio\Hal\ConfigProvider::class` to the aggregator, exactly as shown earlier.
Development-only tools can be injected into `config/development.config.php.dist` instead, so they never reach production.

The same mechanism applies across the ecosystem, including **mezzio-authentication**, **mezzio-authorization**, **mezzio-session**, **mezzio-csrf**, **mezzio-problem-details**, and many Laminas components.
Each one arrives through the same convention and is enabled through the same configuration, so your application, and your AI assistant, only ever have to learn one integration model.

## AI Follows the Lead: A Working Example

Here is what that looks like in practice.
Suppose you ask an assistant to *add an endpoint that lists orders*.

**1. Read the configuration.**
The assistant opens `config/routes.php` and `config/config.php` to see which routes and modules already exist, then opens an existing handler and its test to learn the project's patterns.

**2. Scaffold with mezzio-tooling.**
Rather than wiring classes by hand, it uses **mezzio-tooling**, included in the skeleton:

```bash
composer mezzio mezzio:module:create Orders
composer mezzio mezzio:handler:create "Orders\Handler\ListOrdersHandler"
```

The first command creates the module, its `ConfigProvider`, and its autoloading rule, and registers the module.
The second creates the handler and its factory and registers both in the container.
Related commands cover the rest: `mezzio:middleware:create`, `mezzio:factory:create`, and `mezzio:module:register` and `mezzio:module:deregister`.

**3. Write the logic by mirroring existing code.**
With correct scaffolding in place, the assistant only fills in the application logic.
Following the project's lead, it injects the repository through the constructor, updates the factory to supply it, and maps the route:

```php
// src/Orders/src/Handler/ListOrdersHandler.php
final class ListOrdersHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse($this->orders->findAll());
    }
}

// config/routes.php
$app->get('/orders', Orders\Handler\ListOrdersHandler::class, 'orders.list');
```

It then writes a test modeled on the existing ones:

```php
// test/OrdersTest/Handler/ListOrdersHandlerTest.php
final class ListOrdersHandlerTest extends TestCase
{
    public function testReturnsOrdersAsJson(): void
    {
        $orders = $this->createMock(OrderRepositoryInterface::class);
        $orders->method('findAll')->willReturn([['id' => 1]]);

        $handler  = new ListOrdersHandler($orders);
        $response = $handler->handle($this->createMock(ServerRequestInterface::class));

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(200, $response->getStatusCode());
    }
}
```

**4. Validate.**
Finally, it runs `composer check`.
Coding standards and tests either pass or point to exactly what needs fixing.

None of these steps relied on an AI feature in Mezzio.
The assistant succeeded because the configuration told it what existed, the conventions told it what to write, the tooling handled the wiring, and the standards checked the result.

## Pointing AI to What's Already There

Many AI coding tools read a project-level instructions file, such as `AGENTS.md` or `CLAUDE.md`, before they start working.
In a Mezzio project, that file can stay remarkably short, because it mostly points to things that already exist:

```markdown
# AGENTS.md

- This is a Mezzio application. Read config/pipeline.php, config/routes.php,
  and config/config.php before making changes.
- Scaffold modules, handlers, middleware, and factories with mezzio-tooling
  (`composer mezzio mezzio:<command>`).
- Inject dependencies through constructors and register services in the
  module's ConfigProvider.
- Use existing handlers and tests as examples.
- Run `composer check` before finishing.
```

There is no need to explain a custom architecture or list exceptions to the rules.
The code already documents itself, and the instructions file only has to say "follow it."

## Reviewing AI Output

Using an assistant doesn't change who is responsible for the code.
Choosing components, reviewing generated changes, and keeping the pipeline in order are still the developer's job.
Mezzio makes that job easier.

Because the pipeline and routes are explicit, a reviewer can see exactly what protects each endpoint.
For example, a route that requires authentication declares it directly:

```php
// config/routes.php
$app->get('/orders', [
    Mezzio\Authentication\AuthenticationMiddleware::class,
    Orders\Handler\ListOrdersHandler::class,
], 'orders.list');
```

If the authentication middleware is missing from a route or from `config/pipeline.php`, the omission is visible in the diff.
The same applies to CSRF protection, session handling, and any other middleware.
As more code is generated, being able to audit it at a glance matters more, and Mezzio keeps every decision where a reviewer can see it.

## Conclusion

Mezzio has no AI in its core, and it doesn't need any.
Its core is the example AI follows.

**Configuration** keeps Mezzio lean: every capability is a deliberate choice, recorded in plain PHP that anyone can read.
**Convention** keeps it predictable: PSR standards, `ConfigProvider` classes, factories, and a consistent project structure give every feature the same shape.
**Principles** keep it trustworthy: dependency injection, single responsibility, strict coding standards, and extensive tests give an assistant code worth imitating and a way to verify its work.

With the Laminas components available on demand and mezzio-tooling handling the wiring, Mezzio gives developers and their AI assistants the same thing: a codebase that is clear enough to read, consistent enough to extend, and strict enough to trust.

## Additional Resources

- [Laminas MVC Is Retiring](https://getlaminas.org/blog/2025-06-06-laminas-mvc-is-retiring.html)
- [Mezzio101: Using the Mezzio Skeleton Installer](https://getlaminas.org/blog/2025-01-30-mezzio101-using-mezzio-skeleton-installer.html)
- [Mezzio101: What Defines a Middleware Architecture?](https://getlaminas.org/blog/2025-04-08-mezzio101-what-defines-a-middleware-architecture.html)
- [Laminas Documentation](https://docs.laminas.dev/)
