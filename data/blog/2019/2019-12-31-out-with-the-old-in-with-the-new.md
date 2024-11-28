---
id: 2019-12-31-out-with-the-old-in-with-the-new
author: matthew
title: 'A new project for a new year'
draft: false
public: true
created: '2019-12-31T17:30:00-05:00'
updated: '2020-01-13T13:30:00-05:00'
tags:
  - laminas
---

As the year and decade wrap up, we have some exciting news: we've just completed
migrating the Zend Framework code to the various Laminas projects!

<!--- EXTENDED -->

## Current Status

There's still work to be done, but the actual code migration is complete! You
can find the code in the following locations:

- [Components and MVC](https://github.com/laminas) ([docs](https://docs.laminas.dev))
- [API Tools (formerly Apigility)](https://github.com/laminas-api-tools)
- [Mezzio (formerly Expressive)](https://github.com/mezzio) ([docs](https://docs.mezzio.dev))

We still have a few things to iron out:

- ~~The Packagist API, while it claimed to work and did not return errors, did not
  always abandon the old packages; we'll be auditing those later this
  week.~~

- ~~We will be renaming our Slack workspace later this week to reflect the
  change.~~

- ~~Likewise, we'll be renaming our Discourse and re-pointing it.~~

- Our bot will need some changes to work with the new repos, and we will likely
  be moving tasks such as building documentation to GitHub Actions. (Update:
  documentation is now built via GitHub Actions.)

- We need to rebuild the former Apigility site to reflect the name change, and
  plan to eventually push it to [api-tools.getlaminas.org](https://api-tools.getlaminas.org).

- We need to update the Zend Framework, Apigility, and Expressive websites to
  indicate the changes.

But the hard part is now done, and you can start using the code TODAY!

Speaking of...

## How to migrate to Laminas

If you run a ZF MVC, Apigility, or Expressive application, or even just use any
Zend Framework, Apigility, or Expressive components in your application, we have
a tool that will help you migrate.

Regardless of your scenario, use the following steps.

### 0. Ensure you have an up-to-date Composer

Due to features of Composer our dependency plugin uses, we require Composer
1.7.0 and up. If you're unsure what version you are on, run `composer --version`.
If you are on an older version, run `composer self-update`, or install from
scratch.

### 1. Install laminas-migration

To migrate a project, first install the laminas/laminas-migration package.

#### Via Composer

Install the library globally using [Composer](https://getcomposer.org):

```bash
composer global require laminas/laminas-migration
```

If you choose this option, you will need to ensure that the `vendor/bin/`
subdirectory of your > global Composer installation is in your environment
`$PATH`.

You can find where the global Composer installation is by executing:

```bash
composer global config home
```

On Linux and Mac operating systems, update your shell configuration to add
that path to your `$PATH` environment variable.

> #### Adding to the PATH
>
> The mechanism for adding to your environment `$PATH` variable depends on your
> operating system.
>
> For Linux, Mac, and other *nix variants, you can do so by adding a line like
> the following at the end of your profile configuration file (e.g., `$HOME/.bashrc`,
> `$HOME/.zshrc`, `$HOME/.profile`, etc.):
>
> ```bash
> export PATH={path to add}:$PATH
> ```
>
> For Windows, the situation is a bit more involved; [this HOWTO](https://www.architectryan.com/2018/03/17/add-to-the-path-on-windows-10/)
> provides a good tutorial on the subject.

#### Via cloning

Clone the repository somewhere:

```bash
git clone https://github.com/laminas/laminas-migration.git
```

Install dependencies:

```bash
cd laminas-migration
composer install
```

From there, either add the `bin/` directory to your `$PATH` (see the [note on
adding to the PATH](#adding-to-the-path), above), symlink the
`bin/laminas-migration` script to a directory in your `$PATH`, or create an
alias to the `bin/laminas-migration` script using your shell:

```bash
# Adding to PATH:
export PATH=/path/to/laminas-migration/bin:$PATH
# Symlinking to a directory in your PATH:
cd $HOME/bin && ln -s /path/to/laminas-migration/bin/laminas-migration .
# creating an alias:
alias laminas-migration=/path/to/laminas-migration/bin/laminas-migration
```

### 2. Run the migration command

From there, enter a project you wish to migrate, and run the following:

```bash
laminas-migration migrate
```

You may want to use the `--exclude` or `-e` option one or more times for
directories to exclude from the rewrite; on the ZF website and my own, I used
`-e data`, for instance:

```bash
laminas-migration migrate -e data
```

> #### Module and Config Post Processor injection
>
> If you are migrating an MVC, Apigility, or Expressive application, the
> migration tooling attempts to inject some code in your application. This can
> fail if you have non-standard configuration.
>
> - For MVC and Apigility applications, the migration tooling attempts to add
>   `Laminas\ZendFrameworkBridge` as a module to the top of the
>   `config/modules.config.php` file. If injection fails, add the module in a way
>   appropriate to your application.
>
> - For Expressive applications, the migration tooling attempts to add
>   `Laminas\ZendFrameworkBridge\ConfigPostProcessor` as a post processor class
>   to the `ConfigAggregator` constructor. The `ConfigAggregator` constructor
>   has the following signature:
>
> ```php
> public function __construct(
>     array $providers = [],
>     ?string $cachedConfigFile = null,
>     array $postProcessors = []
> )
> ```
>
> Typically, the structure of the `config/config.php` file in an Expressive
> application looks like the following:
>
> ```php
> $cacheConfig = [
>     'config_cache_path' => 'data/cache/app_config.php',
> ];
>
> $aggregator = new ConfigAggregator([
>     // config providers from 3rd party code
>     // ...
>
>     // App-specific modules
>     // ...
>
>     // Include cache configuration
>     new ArrayProvider($cacheConfig),
>
>     // Load application config in a pre-defined order in such a way that local settings
>     // overwrite global settings. (Loaded as first to last):
>     //   - `global.php`
>     //   - `*.global.php`
>     //   - `local.php`
>     //   - `*.local.php`
>     new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
>
>     // Load development config if it exists
>     new PhpFileProvider('config/development.config.php'),
> ], $cacheConfig['config_cache_path']);
>
> return $aggregator->getMergedConfig();
> ```
>
> As such, the migration tooling rewrites the second to last line to read:
>
> ```php
> ], $cacheConfig['config_cache_path'], [\Laminas\ZendFrameworkBridge\ConfigPostProcessor::class]);
> ```
>
> In most cases, failure to inject means that the individual arguments have
> been pushed to their own line. In such cases, add the third argument as
> detailed above.
>
> In other cases, applications may already be using post processors. If so,
> add `\Laminas\ZendFrameworkBridge\ConfigPostProcessor::class` to the list of
> post processors.

### 3. Install dependencies

Once migration is done and you've added the repository, you can install
dependencies:

```bash
composer install
```

From there, run tests, and report any issues to the [Laminas
slack](https://laminas.dev/chat) in the #laminas-issues
channel, or report directly in the appropriate issue trackers, based on the
components in which you see problems.

(These instructions are now [on the documentation
site](https://docs.laminas.dev/migration/)

## What's next?

As outlined above, we have a number of things to do to finish up the migration.
Additionally:

- We are completing our technical charter with the Linux Foundation.
- We will be starting a Community Bridge campaign to raise some initial
  finances.
- We want to complete our API story for Mezzio.
- We have more async integrations we want to do.
- And, of course, projects to finish, such as separating out adapters from
  components like laminas-cache and laminas-db, PSR-7 integration in the MVC,
  and more.

Stay tuned for more announcements, and Happy New Year! Let's build great things
this next year and decade!
