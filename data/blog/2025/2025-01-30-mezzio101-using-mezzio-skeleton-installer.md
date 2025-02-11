---
id: 2025-01-30-mezzio101-using-mezzio-skeleton-installer
author: bidi
title: 'Mezzio 101:  Create an Application Using the Mezzio Skeleton Installer'
draft: false
public: true
created: '2025-01-30T11:00:00-01:00'
updated: '2025-01-30T11:00:00-01:00'
tags:
  - mezzio
  - skeleton
  - tutorial
---

Creating a site from scratch has always been a daunting task for developers.
The difficulty doesn't come so much from the complex coding, but from the imprecise specifications.
Even if you start with clear goals in mind, the project can grow exponentially in complexity as time goes by.
New technical requirements, new features, bug fixes and refactoring all get in the way of the developer resting on his laurels.

One might argue the above are only possible in the programming world.
We aren't building bridges here.

We need a reliable, well-maintained starting point for our projects.
This scaffolding should be a basis that allows us to expand as needed in the future.

<!--- EXTENDED -->

> This will be the first article of a series meant to showcase how to build a fully-operational application using Mezzio.

### Where to start?

Building the project manually is a complex operation that has too many moving parts to be accessible.
Also, each of us will build it in his own image and that invites chaos even if we have the best intentions.
A better solution is using the Mezzio Skeleton Installer that handles a lot of aspects automatically, the most basic being listed below:

- Standard directory and file structure
- Routing
- Middleware
- Templating
- CI/CD

The Laminas Project is known to offer a PHP framework and component library that is kept up-to-date with the latest updates in the PHP ecosystem.
A second, but highly important aspect is their focus on standards defined by the [PHP Framework Interop Group](https://www.php-fig.org/).

#### Prerequisites

You need a working installation of [PHP](https://www.php.net/manual/en/install.php) and [Composer](https://getcomposer.org/) available on your `$PATH`.

### What is Mezzio?

As mentioned on [the Zend official site](https://www.zend.com/resources/what-mezzio-why-would-i-use-it), 'Mezzio is the Laminas middleware runtime -- previously known as Expressive in the Zend framework.'
Mezzio is designed around PHP-FIG, the PHP Framework Interop Group, that has defined several standards recommendations.
The goal of the standards is to ensure that PHP code is written in a consistent, interoperable, and maintainable way.

### What is middleware?

Middleware is code that is executed between the request and response.
Most commonly, it performs these tasks, but more layers can be defined based on your application:

- Aggregates incoming data.
- Processes the request.
- Creates and returns a response.

There are several advantages to using middleware:

- It enables old systems to interface with newer ones.
- It's scalable.
- Integrations are created more easily.
- It enables automating processes.

### Installing Mezzio

Let's put aside the technical stuff in favor of some real work that you can be proud of.
Building things with our hands, kind of.

To start, create a folder where you want your project files to be created.

```shell
mkdir -p ~/Projects/my-mezzio-project
```

The next command will launch the Mezzio Skeleton Installer.
We will go through the installation process together, using the recommended settings.
We will opt primarily for the Laminas-supported options to maintain consistency.
Funnily enough, the installation should complete in less time than it takes for you to read this tutorial.

```shell
composer create-project mezzio/mezzio-skeleton ~/Projects/my-mezzio-project
```

You should see the text and prompt below:

![step-1-installation-type](/images/blog/mezzio101/mezzio101-use-skeleton-01.jpg "step-1")

Submit `3` for option `Modular`.

![step-2-dependency-injection](/images/blog/mezzio101/mezzio101-use-skeleton-02.jpg "step-2")

Submit `1` for option `laminas-servicemanager`.
Just as a side note, this option is Laminas's factory-driven dependency injection container.
Its version 4.0 supports PSR-11: Container interface version 1.1 and 2.

![step-3-router](/images/blog/mezzio101/mezzio101-use-skeleton-03.jpg "step-3")

Submit `1` for `fastroute`.

![step-4-templating](/images/blog/mezzio101/mezzio101-use-skeleton-04.jpg "step-4")

Submit `2` for `Twig`.

![step-5-error-reporting](/images/blog/mezzio101/mezzio101-use-skeleton-05.jpg "step-5")

Submit `1` for `Whoops`.
At this point, the installer has enough information to install the 3rd party packages in the application.

You can expect to see this prompt regarding dependency injection.

![step-6-injection](/images/blog/mezzio101/mezzio101-use-skeleton-06.jpg "step-6")

Submit `1` for `config/config.php`.

![step-7-injection-confirmation](/images/blog/mezzio101/mezzio101-use-skeleton-07.jpg "step-7")

Submit `y` for `Remember this option for other packages of the same type? (Y/n)`.

The final step in the installation performs the following operations:

- Sets up `phpcs` the [PHP Code Sniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer)
- Clears the cache
- Enables development mode

![step-8-installation-complete](/images/blog/mezzio101/mezzio101-use-skeleton-08.jpg "step-8")

#### The result of your work so far

To run the application, you need to navigate to the installation folder using the command below.

```shell
cd ~/Projects/my-mezzio-project/
```

Then start the web server for your project.

```shell
php -S 0.0.0.0:8080 -t public
```

Now go to your favorite browser and type this URL `http://localhost:8080/`.

If everything worked correctly, you should now have a working application like in the screenshot below.
You can then visit the recommended links to learn more about using the components we chose during the installation.

![step-9-installation-complete](/images/blog/mezzio101/mezzio101-use-skeleton-09.jpg "step-9")

### Additional Resources

- Learn more about [Laminas](https://docs.laminas.dev/).
- Learn more about [Mezzio](https://docs.mezzio.dev/) and its [features](https://docs.mezzio.dev/mezzio/v3/getting-started/features/).
- Learn more about [Mezzio's fundamentals from Matthew Setter's book 'Mezzio Essentials'](https://mezzioessentials.com/).
