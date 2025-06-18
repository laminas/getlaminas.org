---
id: 2025-06-06-laminas-mvc-is-retiring
author: bidi
title: 'Laminas MVC Is Retiring'
draft: false
public: true
created: '2025-06-06T11:00:00-01:00'
updated: '2025-06-06T11:00:00-01:00'
tags:
  - MVC
  - middleware
  - framework
  - software architecture
  - software lifecycle
openGraphImage: '2025-06-06-laminas-mvc-is-retiring.png'
openGraphDescription: 'Laminas MVC Is Retiring'
---

The Laminas MVC (Model-View-Controller) framework has proven itself over the years as a viable solution for enterprise applications.
Given its versatility, it was seen as a good starting point by many professional PHP developers.
However, the Laminas Technical Steering Committee (TSC) have decided to discontinue active development of Laminas MVC.

<!--- EXTENDED -->

Laminas MVC will be marked as `security-only` until PHP 8.5 is released _(This is likely to be around November 2025)_, and abandoned in Packagist after this date.
Projects in `security-only` mode only receive fixes for security vulnerabilities.
Once MVC is abandoned, no additional releases will be made.
In this article we will explore the reasoning behind the decision taken by the Laminas TSC members.

### Why Discontinue Laminas MVC?

The **main reason** for the discontinuation of MVC is that there is simply **no time and resources** to maintain it.
This is reinforced by the fact that **MVC has had little activity** in the past year - by users, contributors and maintainers.
Many of the TSC members consider [Mezzio](https://github.com/mezzio/mezzio) and PSR-15 as a superior paradigm for web-based software development to the MVC pattern with its event driven mutable state.
Quite simply, most TSC members moved to Mezzio a number of years ago and never looked back.

TSC members have shifted their focus to [Mezzio](https://github.com/mezzio/mezzio) as the better alternative, a worthy successor to MVC and the **second reason** for their decision regarding MVC.
Having the **Mezzio alternative** ready to use makes letting go of MVC a lot easier.
Mezzio and its dependencies are much easier to maintain, and this framework has become the de facto solution for new projects of late.

### The Steps Leading to the Eventual Discontinuation of MVC

A major decision like discontinuing a central product cannot be taken lightly.
The TSC members agree that Laminas MVC **cannot be archived** just yet, because it is **still in use** by many active applications.
Instead it's going to be put in `security-only` mode until PHP 8.5 is released, to allow developers of legacy applications to update their code by the time MVC is fully abandoned and archived.
The shift of focus toward **middleware**, PSR-7, PSR-15 and PSR-17 will be **actively publicized** as a collective effort by TSC members moving forward.

### The Middleware Alternative

As we have already mentioned, **Mezzio** is the recommended alternative to replace MVC.

Some of the main selling points of Mezzio include:

- Better and easier testability.
- Easy entry with a flat learning curve.
- Less framework coupling.
- Improved interoperability with the wider ecosystem.
- Better performance.
- Less complexity.
- Much improved long-term maintainability.
- Mezzio implements **PSRs** defined by the PHP Framework Interop Group to promote code consistency, leading to better readability, testability and maintainability:
    - [PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/).
    - [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/).
- Mezzio supports several **[PSR-11 Container](https://github.com/php-fig/container)** implementations:
    - laminas-servicemanager.
    - Symfony DI Container.
    - PHP-DI.
    - chubbyphp-container.
- There is a long list of Laminas and third-party packages that can be integrated into your project: _You decide_ which components to include in your application.
- The **Mezzio microframework** has a minimalist structure with 5 core components, which makes it easier to work with than MVC:
    - DI container.
    - Router.
    - Error handler for the development environment.
    - Template engine.
    - Handlers (based on PSR-7 and PSR-15 interfaces).
- Mezzio can be used to **build any type of application**, whether it be an API or an HTML-based frontend.

Basically, anything you can do with Laminas MVC can be done easier and better with Mezzio.

### How to Migrate from Laminas MVC to Mezzio

There is no "One-size-fits-all" approach to migrating an MVC application to Mezzio.
Some of the possible approaches include:

- Slowly migrating controllers to middleware pipelines using [laminas-mvc-middleware](https://docs.laminas.dev/laminas-mvc-middleware/).
- Wrapping the entire application in middleware to implement the strangler pattern.
- Performing a complete re-write of the application layer.

The approach chosen depends on many factors such as how much coupling to framework code exists in your codebase and whether you have practiced dependency injection as your application has evolved.

The [Laminas discourse forum](https://discourse.laminas.dev/) is always available for community Q and A.

### Additional resources

- [Mezzio](https://github.com/mezzio/mezzio)
- [What Is Mezzio? Why Would I Use It?](https://www.zend.com/resources/what-mezzio-why-would-i-use-it)
- [Laminas ServiceManager 4](https://github.com/laminas/laminas-servicemanager)
- [PSR-11: Container interface](https://www.php-fig.org/psr/psr-11/)
- [PSR-11: Container interface - version 2 on GitHub](https://github.com/php-fig/container)
- [PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/)
- [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/)
- [TSC Minutes 2025-05-05](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2025-05-05-TSC-Minutes.md)
