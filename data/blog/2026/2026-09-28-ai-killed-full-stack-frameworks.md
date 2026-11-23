---
id: 2026-09-28-ai-killed-full-stack-frameworks
author: bidi
title: 'AI Killed Full-Stack Frameworks: The Mezzio Approach'
draft: false
public: true
created: '2026-09-28T11:00:00-01:00'
updated: '2026-09-28T11:00:00-01:00'
tags:
  - framework
  - ai-coding
  - psr-15
  - modular-architecture
  - skeleton
openGraphImage: '2026-09-28-ai-killed-full-stack-frameworks.png'
openGraphDescription: 'AI Killed Full-Stack Frameworks: The Mezzio Approach'
---

# AI Killed Full-Stack Frameworks: The Mezzio Approach

## Introduction

Full-stack frameworks such as **Laravel** and **Symfony** have long been a popular choice for PHP web applications.
They bundle an ORM, authentication, routing, templating, and much more, which made sense when every line of infrastructure code had to be written by hand.

That trade-off is changing.
**AI coding assistants can generate boilerplate on demand**, so shipping a large framework where much of the code goes unused is harder to justify.

> A minimal core, extended one package at a time, is becoming the more practical starting point.

|                     | Full-stack framework       | Minimal skeleton           |
|---------------------|----------------------------|----------------------------|
| **Starting point**  | Everything included        | Routing and DI only        |
| **Adding features** | Already there, used or not | Install packages on demand |
| **Structure**       | Framework's conventions    | Your choices               |
| **AI context**      | Large, hidden "magic"      | Small, explicit, readable  |

The Laminas Project has been moving in this direction for some time.
As **Laminas MVC** heads toward end of life, the Laminas Technical Steering Committee recommends **Mezzio**, its PSR-15 middleware microframework, as the alternative (see [Laminas MVC Is Retiring](https://getlaminas.org/blog/2025-06-06-laminas-mvc-is-retiring.html)).
Mezzio starts with very little and lets you add features as your application needs them.

## The Problem with Full-Stack Giants

**Bloat and Overhead:** A full-stack framework ships features whether your application uses them.
Each one adds dependencies to install, configure, update, and secure.

**Rigid Structures:** Conventions that help at the start can get in the way when a project needs something different.
In controller-centric designs, a request targets a single controller that must handle all the logic, which makes reordering or extending the request lifecycle harder later on.

**AI Redundancy:** Database connections, route definitions, and configuration arrays are exactly the kind of repetitive code AI generates well.
A framework that pre-builds them offers less value than it once did.

## The Modular Shift: Skeletons Over Monoliths

**Start Small:** The recommended way to begin is the **Mezzio skeleton installer**:

```bash
composer create-project mezzio/mezzio-skeleton mezzio
```

Rather than making architectural decisions on your behalf, the installer asks you to choose:

- **Application structure:** minimal (no default middleware), flat (all code under `src/`, the default), or modular (each directory under `src/` is a module with its own code, templates, and configuration).
- **Dependency injection container:** laminas-servicemanager is recommended.
- **Router:** FastRoute is recommended.
- **Template renderer:** Plates, Twig, or laminas-view, or none at all for an API.
- **Error handler:** Whoops provides detailed, browsable error information during development.

For a guided walkthrough, the Mezzio 101 series starts with [using the Mezzio skeleton installer](https://getlaminas.org/blog/2025-01-30-mezzio101-using-mezzio-skeleton-installer.html).

**Add on Demand:** The skeleton includes the pieces that make adding features straightforward:

- **laminas-component-installer:** a Composer plugin that looks for an `extra.laminas.config-provider` entry in any package you install and, if found, offers to register that provider in `config/config.php`.
- **laminas-config-aggregator:** merges configuration from every registered `ConfigProvider` in order, with later entries taking precedence, and supports configuration caching.
- **Module tooling:** `composer mezzio mezzio:module:create <name>` scaffolds a new module, adds its autoloading rule, and registers its `ConfigProvider`.

Here is the component installer in action.
Adding HAL support to an API starts with one command:

```bash
composer require mezzio/mezzio-hal
```

Because mezzio-hal declares its config provider, the installer asks where to inject it.
Selecting `config/config.php` adds the provider for you:

```php
// config/config.php
$aggregator = new ConfigAggregator([
    Mezzio\Hal\ConfigProvider::class, // injected by laminas-component-installer
    App\ConfigProvider::class,
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
], 'data/config-cache.php');
```

Development-only tools can be injected into `config/development.config.php.dist` instead, so they never reach production.

**Total Control:** Mezzio keeps the request lifecycle in plain PHP files you can read end to end.
`config/pipeline.php` defines the middleware pipeline, and `config/routes.php` maps paths to handlers:

```php
$app->get('/hello', App\Handler\HelloHandler::class, 'hello');
```

Middleware implements PSR-15's `MiddlewareInterface` with a single `process()` method, and handlers implement `RequestHandlerInterface` with a single `handle()` method.
There is no hidden kernel for developers, or AI tools, to guess about.
To learn more about how the pipeline works, read [Mezzio101: What Defines a Middleware Architecture?](https://getlaminas.org/blog/2025-04-08-mezzio101-what-defines-a-middleware-architecture.html).

## Why Mezzio Fits This Era

**Built for Extensibility:** Mezzio is built on PHP-FIG standards rather than proprietary abstractions:

- **PSR-7** HTTP messages, **PSR-11** containers, **PSR-15** middleware, and request handlers
- Swappable containers: **laminas-servicemanager**, **Pimple**, or **Aura.Di**
- Swappable routers: **FastRoute**, **Aura**, or **laminas-router**
- Swappable template renderers: **Plates**, **Twig**, or **laminas-view**

**Modular by Design:** Applications can grow at their own pace:

- **Flat structure** keeps small projects simple.
- **Modular structure** separates features into modules with their own code, templates, configuration, and assets.
- **Reusable modules** can later be repackaged and shared across projects.

**AI-Friendly:** A small, explicit codebase leaves less room for AI tools to misread how an application works.
**mezzio-tooling**, included in the skeleton, handles the wiring AI shouldn't have to guess:

- `mezzio:middleware:create` generates a PSR-15 middleware, its factory, and the container registration.
- `mezzio:handler:create` does the same for request handlers, and creates a template when a renderer is installed.
- `mezzio:module:create`, `mezzio:module:register`, and `mezzio:module:deregister` manage modules and their autoloading rules.

AI can focus on application logic while the tooling handles the plumbing.

**Production-Ready From Day One:** The skeleton ships with:

- **Testing and code style:** PHPUnit and PHP_CodeSniffer, both run with `composer check`
- **Security:** Roave's security-advisories package blocks dependencies with known vulnerabilities
- **Performance:** configuration caching enabled by default
- **Development mode:** `composer development-enable` and `composer development-disable` toggle development-only configuration and modules

## When a Full-Stack Framework Still Makes Sense

Full-stack frameworks remain a solid choice in many situations.
Their conventions give large teams a shared vocabulary, and their ecosystems offer a ready answer for most common problems.
If an application fits their defaults closely, or a team already knows one of them well, the cost of switching may outweigh the benefits.

A minimal core also asks more of its developers.
Choosing packages, reviewing AI-generated code, and keeping the pipeline in order are all your responsibility.
For many projects that flexibility is exactly the point, but it is worth weighing before starting.

## Conclusion

Full-stack frameworks were designed for a time when infrastructure code was slow to write by hand.
With AI now generating much of that code, the more valuable foundation is one that stays small, follows standards, and is straightforward to read.
Mezzio offers exactly that: a PSR-15 pipeline, your choice of components, and tooling that keeps everything wired together.

## Additional Resources

- [Laminas MVC Is Retiring](https://getlaminas.org/blog/2025-06-06-laminas-mvc-is-retiring.html)
- [using the Mezzio skeleton installer](https://getlaminas.org/blog/2025-01-30-mezzio101-using-mezzio-skeleton-installer.html)
- [Mezzio101: What Defines a Middleware Architecture?](https://getlaminas.org/blog/2025-04-08-mezzio101-what-defines-a-middleware-architecture.html)
- [Laminas Documentation](https://docs.laminas.dev/)
