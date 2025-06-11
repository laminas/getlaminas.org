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
Still, a number of reasons have determined the members of the Laminas Technical Steering Committee (TSC) to discontinue Laminas MVC.

<!--- EXTENDED -->

In the first stage, which lasts until PHP 8.5 is released (estimated at November 2025), MVC will be marked as `security-only` (fixing security vulnerabilities) and after that it will be `archived` (no longer supported).
In this article we will explore the reasoning behind the decision taken by the Laminas TCS members.

### Why discontinue Laminas MVC?

The **main reason** why MVC is set to be discontinued is that there is simply **no time and resources** to maintain it.
This is reinforced by the **little activity on Laminas MVC** in the past few months - by users, contributors and maintainers.
Many TSC members are seeing Laminas MVC as a legacy code that is more difficult to work with.
Some have expressed a lack of desire to return to Laminas MVC after using **Mezzio** middleware.

TSC members have shifted their focus to [Mezzio](https://github.com/mezzio/mezzio) which is the better alternative, a worthy successor to MVC and the **second reason** for their decision regarding MVC.
Having the **Mezzio alternative** ready to use makes letting go of MVC a lot easier.
Mezzio and its dependencies are already a handful to maintain, but this framework has become the de facto solution for new projects of late.

### The steps leading to the discontinuation on MVC

A major decision like discontinuing a central product cannot be taken lightly.
The TSC members agree that Laminas MVC **cannot be archived** just yet, because it is **still in use** by many active applications.
Instead it's going to be put in `security-only` mode until PHP 8.5 is released, to allow developers of legacy applications to update their code by the time MVC is fully abandoned and archived.
The shift of focus toward **middleware**, PSR-7, PSR-15 and PSR-17 will be **actively publicized** as a collective effort by TSC members moving forward.

### The middleware alternative

As we have already mentioned, **Mezzio** is the recommended alternative to replace MVC.

- Mezzio implements **PSRs** defined by the PHP Framework Interop Group to promote code consistency that leads to better code readability and maintainability:
    - [PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/).
    - [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/).
- Mezzio supports several **[PSR-11 Container](https://github.com/php-fig/container)** implementations:
    - laminas-servicemanager.
    - Symfony DI Container.
    - PHP-DI.
    - chubbyphp-container.
- There is a long list of **Laminas packages** that can be easily integrated into your project.
They also implement PSRs.
You decide what you want components to include in your application.
- The **Mezzio microframework** has a minimalist structure with 5 core components, which makes it easier to work with than MVC:
    - DI container.
    - Router.
    - Error handler for the development environment.
    - Template engine.
    - Handlers (based on PSR-7 and PSR-15 interfaces).
- Mezzio can be used to **build any type of application**, whether it be an API or an HTML-based frontend.

Basically, anything you can do with Laminas MVC can be done easier and better with Mezzio.

### How to migrate from Laminas MVC to Mezzio

A laminas-mvc based application can be prepared to make the migration to Mezzio easier.
The middleware and request handlers can already be used in the existing laminas-mvc application, which can then be adapted.
The best course of action is to **consult a TSC member** for advice on moving forward.
Alternatively, you can contact a commercial vendor to analyze your application and figure out a customized solution.

### Additional resources

- [Mezzio](https://github.com/mezzio/mezzio)
- [What Is Mezzio? Why Would I Use It?](https://www.zend.com/resources/what-mezzio-why-would-i-use-it)
- [Laminas ServiceManager 4](https://github.com/laminas/laminas-servicemanager)
- [PSR-11: Container interface](https://www.php-fig.org/psr/psr-11/)
- [PSR-11: Container interface - version 2 on GitHub](https://github.com/php-fig/container)
- [PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/)
- [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/)
- [TSC Minutes 2025-05-05](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2025-05-05-TSC-Minutes.md)
