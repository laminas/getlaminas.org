---
id: 2020-03-09-transferring-zf-to-laminas
author: michal-bundyra
title: 'Transferring Zend Framework to Laminas: A Retrospective.'
draft: false
public: true
created: '2020-03-09T13:00:00+00:00'
updated: '2020-03-09T13:00:00+00:00'
tags:
    - laminas
    - transfer
    - zend framework
---

# Transferring Zend Framework to Laminas: A Retrospective.

Back in October 2018, Rogue Wave
[announced reorganization of its Zend portfolio, including Zend Framework](https://mwop.net/blog/2018-10-17-long-live-zf.html).
The Zend Framework community was understandably shaken, and many were concerned
about the future of the framework.
Six months later, in April of 2019, Rogue Wave [announced they would be
transferring the project](https://framework.zend.com/blog/2019-04-17-announcing-laminas.html)
to the [Linux Foundation](https://www.linuxfoundation.org/) as the Laminas
Project.

A couple months before the official announcement about transformation, in
February 2019, [we started working](https://github.com/michalbundyra/laminas-transfer/commit/3e253840eafee73af20768567ae7f8bdd7ec4d7d)
on a [tool for transferring the project repositories to their new homes](https://github.com/michalbundyra/laminas-transfer). At
the beginning, everyone would think the tool should be relatively simple,
as we "just" need to change namespaces. But our goal was much greater: we
wanted to provide packages fully compatible with legacy Zend Framework
components. We wanted that our new components to replace legacy components
(yes, we wanted to use [`replace` in `composer.json`](https://getcomposer.org/doc/04-schema.md#replace)).

We needed a tool capable of much more than just "rewriting namespaces".

## Lifetime decisions

### Rewrite whole history or just tags?

We had over 150 components to move, many of them huge components with 8 years of
history and thousands of commits. Some were much smaller - particularly the
Expressive components - with only a few years of history and a few hundred
commits.

The first plan was to rewrite the whole history, every commit by using the [`git
filter-branch`](https://git-scm.com/docs/git-filter-branch) command. This
command allows applying arbitrary actions on every revision of the repository
history. Unfortunately, as the transfer tool's functionality and scope grew,
this operation became prohibitively slow; rewriting even a single component took
several hours.

As such, we decided on a different approach: we decided to rewrite only tags.
Since users would not be able to pin to existing commits anyways (as
filter-branch creates new signatures, and thus new commit identifiers), we
really only needed to worry about specific tags, which then get translated into
installable releases. This also ensures it's easier to identify the full history
of any given commit, which is useful to maintainers.

So we changed our process: we checked out each tag, performed our rewrite
operations, dropped the original tag, and re-issued the tag with the rewritten
code.

### Splitting projects

Apigility was a separate project under a separate organization. But Expressive
was bundled under the same organization as the MVC and general components. We
decided it should have its own organization, just like Apigility already did.

Additionally, we felt the organization names for these subprojects should match
their new names: Laminas API Tools and Mezzio.

As such, we now have the following GitHub organisations representing the
Laminas Project:

- [laminas](https://github.com/laminas), representing components and the MVC.
- [laminas-api-tools](https://github.com/laminas-api-tools), representing the
  Laminas API Tools components (formerly Apigility).
- [mezzio](https://github.com/mezzio), representing the Mezzio middleware
  runtime and components (formerly Expressive).

This makes it simpler to find code related specifically to each subproject.

### Unification

With the new project we wanted to keep consistent namespaces for all components.

As an example, here are some of the previous namespaces represented in Zend
Framework packages:

- `Zend`
- `Zend\Expressive`
- `ZendXml`
- `ZendOAuth`
- `ZendService\{service component namespace}`
- `ZF`
- `ZF\Apigility`

This situation was often confusing even to the maintainers! As such, we
standardized on two top-level namespaces:

- `Laminas`
- `Mezzio`

We consider Laminas API Tools a subnamespace, as it builds on the Laminas MVC,
so all components under that subproject now have the namespace
`Laminas\ApiTools`. Service components such as `ZendOAuth` and
`ZendService\Twitter` are now Laminas components, so they get namespaces such as
`Laminas\OAuth` and `Laminas\Twitter`, which are now in line with other
components.

### Deprecation and abandoning packages

Hard as the decision was, we also decided to abandon some packages. These
include:

- [`ZendService\Amazon`](https://github.com/zendframework/ZendService_Amazon): 
  we suggest using the official [AWS PHP SDK](https://github.com/aws/aws-sdk-php)

- [`ZendService\Google\Gcm`](https://github.com/zendframework/ZendService_Google_Gcm)

- [`ZendService\Apple\Apns`](https://github.com/zendframework/ZendService_Apple_Apns)

We simply did not have resources to update them to the latest changes of their
respective APIs. We would be happy to bring them back to the Laminas Project
if we find people who want to maintain them.

Additionally, we decided to deprecate some other minor packages, not used by our
other components. These include:

- [`zfcampus/zf-console`](https://github.com/zfcampus/zf-console), for which
  we have been directing users to [symfony/console](https://github.com/symfony/console)
  or other CLI tooling libraries.
- [`zfcampus/zf-deploy`](https://github.com/zfcampus/zf-deploy), which was
  quite limited, and needs a complete re-think.

## Making it all work

### Bridge for all components

> The final result is the [laminas-zendframework-bridge](https://github.com/laminas/laminas-zendframework-bridge)
> component required by all migrated components.

Firstly, we needed to provide a compatibility layer to allow third-party
components to work with Zend Framework and Laminas components at the same time.
Our goal was that switching to Laminas should not require a BC break in
third-party libraries. There were a couple challenges:

1. Loading the appropriate Laminas class if the requested Zend Framework class
   does not exist. This was pretty easy - we achieved it by creating
   [an autoloader](https://github.com/laminas/laminas-zendframework-bridge/blob/master/src/Autoloader.php#L111-L155)
   that changes the namespace on the fly, and simultaneously creates
   an alias for the legacy class using [`class_alias`](https://www.php.net/manual/en/function.class-alias.php).
   This approach assures that subsequent requests for the same class use the
   Laminas replacement instead.

   However, autoloading is not triggered for classes referenced by typehints,
   which created the second challenge.

2. Ensuring typehints for legacy classes work with Laminas replacements. As an
   example, please consider the following code:

```php
namespace ThirdParty\Component;

use Zend\ServiceManager\ServiceLocatorInterface;

interface MyInterface
{
    public function run(ServiceLocatorInterface $sm);
}
```

   We wanted to ensure code such as this works when the Laminas component
   replacing the ZF component is installed. To accomplish this, we created an
   [additional autoloader](https://github.com/laminas/laminas-zendframework-bridge/blob/master/src/Autoloader.php#L111-L155),
   that creates a `class_alias` of the legacy class to the Laminas equivalent
   any time a Laminas class is autoloaded.

There was also additional difficulty: we had some integration classes
with "Zend" in the name (see for example [`LaminasRouter`](https://github.com/mezzio/mezzio-laminasrouter/blob/master/src/LaminasRouter.php#L38)).
We were able to resolve these by including class maps to use when resolving
classes with the above autoloaders.

### Custom functions

In several libraries we ship, we have defined namespaced functions.
These pose a problem as there is no equivalent of `class_alias` for
functions. We needed to keep the previous function in its legacy namespace,
and have it delegate to its equivalent in the new namespace.

To accomplish this, the tooling duplicates each function file using the suffix
`.legacy.php` (e.g., `normalize_server.php` would get duplicated to
`normalize_server.legacy.php`), and we use the legacy namespace in that file,
and modify the function to proxy to the function in the new namespace. We then
add these legacy function files to the autoloader, as additions to the existing
ones. This approach allows using the legacy functions side-by-side with the new
versions.

For a more complete examples:

- The rewritten [`create_uploaded_file.php`](https://github.com/laminas/laminas-diactoros/blob/master/src/functions/create_uploaded_file.php)
- Its legacy
[`create_uploaded_file.legacy.php`](https://github.com/laminas/laminas-diactoros/blob/master/src/functions/create_uploaded_file.legacy.php) file,
- And an updated [`composer.json`](https://github.com/laminas/laminas-diactoros/blob/master/composer.json#L53-L70).

> See the transfer tool code: [FunctionAliasFixture](https://github.com/michalbundyra/laminas-transfer/blob/master/src/Fixture/FunctionAliasFixture.php). 

### Custom constants

In a similar vein to namespaced functions, we ran into an issue with namespaced
constants. The solution for these was the same as for functions, fortunately.

For example, see [`constants.php`](https://github.com/mezzio/mezzio/blob/master/src/constants.php)
and [`constants.legacy.php`](https://github.com/mezzio/mezzio/blob/master/src/constants.legacy.php)
from the mezzio/mezzio package.

> See the transfer tool code: [NamespacedConstantFixture](https://github.com/michalbundyra/laminas-transfer/blob/master/src/Fixture/NamespacedConstantFixture.php).

### Container / Service Manager keys

Many components provide [configuration](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/)
for the DI container (generally our [Service Manager](https://docs.laminas.dev/laminas-servicemanager/)
or a [PSR-11 Container](https://www.php-fig.org/psr/psr-11/)). This allows
retrieving services using code like:

```php
$serviceManager->get(ClassName::class);
```

or:

```php
$container->get(ClassName::class);
```

Interestingly, the `::class` notation does not trigger autoloading; even
worse - the class before `::class` does not even need to exist! PHP expands the
string according to the current namespace and imports, without validating it
exists.

What we wanted to accomplish is to have:

```php
$container->get(\Zend\ClassName::class);
```

and:

```php
$container->get(\Laminas\ClassName::class);
```

produce the same result; exactly the same instance, not just a new instance.

Why? Because while a user might migrate their project to Laminas, some
third-party libraries they use might still use the legacy names.

If you know a bit of our [Service Manager configuration](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/),
the solution is relatively easy: you provide [`aliases`](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/#aliases)
mapping the legacy classes to their Laminas equivalent.

However, it's not quite that simple in practice. Container configuration can
come from a variety of sources, even with a given package:

- An external php file, such as [`server.config.php`](https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/config/server.config.php#L14-L29),
- A [`Module` class](https://github.com/laminas/laminas-mvc-middleware/blob/master/src/Module.php#L22-L33)
- A [`ConfigProvider` class](https://github.com/laminas/laminas-inputfilter/blob/master/src/ConfigProvider.php#L31-L44).

We needed to ensure each of these dependency configuration locations would get
rewritten.

> #### Delegator Factories
>
> Unfortunately we were not able to do the same for [delegator factories](https://docs.laminas.dev/laminas-servicemanager/delegators/).
> Delegators must be defined on the original class, not on an alias.
>
> If a library provides a delegator for `\Zend\ClassName` but you are using
> `\Laminas\ClassName`, the legacy delegator will not be triggered. You will need
> to update your own configuration to add it.

> See the transfer tool code: [DIAliasFixture](https://github.com/michalbundyra/laminas-transfer/blob/master/src/Fixture/DIAliasFixture.php).

### Plugin Managers

Related to the previous point, many components provide [plugin managers](https://docs.laminas.dev/laminas-servicemanager/plugin-managers/),
which your own code or third-party libraries can provide configuration for as
well. The configuration is the same, but done in different locations. Adding
more difficulty, components providing a plugin manager often define the
plugin manager configuration directly in the plugin manager definition.

Our solution here was to alter the plugin manager classes during rewrite to
alias the legacy ZF classes to their Laminas equivalents. Doing so allows them
to work without any further changes, fortunately!

For an example, you can inspect the additional aliases in the
[`FilterPluginManager`](https://github.com/laminas/laminas-filter/blob/master/src/FilterPluginManager.php#L200-L255).

> Adding difficulty to this scenario is the fact that plugin manager definitions
> changed from their first introduction to their latest release, particularly as
> they were updated to target the version 3 release of our service manager. Our
> tool had to accommodate these changes!

> See the transfer tool code: [PluginManagerFixture](https://github.com/michalbundyra/laminas-transfer/blob/master/src/Fixture/PluginManagerFixture.php).

### Factories

The next challenge we had with [factory classes](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/#factories).
Many components provide factories for services for use with the service
manager. Often these factories are using other services configured in the DI
Container, as well as the configuration service itself. Consider the
following example:

```php
class ExampleFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $otherService = $container->get(\Zend\OtherService::class);

        return new Example($otherService);
    }
}
```

While our work to provide aliases means that this code should continue to work,
there's one catch: resolving aliases is the slowest operation the service
manager performs. As such, our migration tooling rewrites these references:

```php
class ExampleFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $otherService = $container->get(\Laminas\OtherService::class);

        return new Example($otherService);
    }
}
```

But what about more complex scenarios, like this one:

```php
class ExampleFactory
{
    public function __invoke(ContainerInterface $container)
    {
        if (! $container->has(\Zend\OtherService::class)) {
            throw new MissingDependencyException();
        }

        return new Example($container->get(\Zend\OtherService::class));
    }
}
```

Here, we also want to be able to use the equivalent Laminas service, if defined,
falling back to the legacy service if not. The rewrite tooling thus produces:

```php
class ExampleFactory
{
    public function __invoke(ContainerInterface $container)
    {
        if (! $container->has(\Laminas\OtherService::class)
            && ! $container->has(\Zend\OtherService::class)
        ) {
            throw new MissingDependencyException();
        }

        return new Example(
            $container->has(\Laminas\OtherService::class)
                ? $container->get(\Laminas\OtherService::class)
                : $container->get(\Zend\OtherService::class)
        );
    }
}
```

We had even more complicated examples; look at the
[`SwooleRequestHandlerRunnerFactory`](https://github.com/mezzio/mezzio-swoole/blob/master/src/SwooleRequestHandlerRunnerFactory.php)
or [`HalResponseFactoryFactory`](https://github.com/mezzio/mezzio-hal/blob/master/src/HalResponseFactoryFactory.php)
to see how complicated it got!

While we don't like nesting ternaries, in many cases, it was the
most consistent way to accomplish our ends.

> See the transfer tool code: [LegacyFactoriesFixture](https://github.com/michalbundyra/laminas-transfer/blob/master/src/Fixture/LegacyFactoriesFixture.php). 

### Configuration keys - config postprocessor and configuration merge listener

Many factories also consume and reference specific configuration. Usually
components provide default configuration, and the user must adjust it for a
specific case. Default configuration is usually provided under a key named after
the component itself. As an example:

```php
// Default module configuration:
return [
    'zend-expressive-hal' => [
        'metadata-factories' => [
            ResourceMetadata::class => ResourceMetadataFactory::class,
        ],
    ],
];
```

```php
// Custom user configuration:
return [
    'zend-expressive-hal' => [
        'metadata-factories' => [
            CustomCollectionMetadata::class => CustomCollectionMetadataFactory::class,
        ],
    ],
];
```

A factory which consumes the above configuration might look like this:

```php
class MetadataMapFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];

        $metadataMapConfig = $config[\Zend\Expressive\Hal\MetadataMap::class] ?? [];
        $metadataFactories = $config['zend-expressive-hal']['metadata-factories'] ?? [];

        return new \Zend\Expressive\Hal\MetadataMap($metadataMapConfig, $metadataFactories);
    }
}
```

As you can see here, we have two strings in the configuration we want to change:
`\Zend\Expressive\Hal\MetadataMap::class` and `zend-expressive-hal`.

And here is the problem: we can rename them here in the factories, but then all
configuration provided by third-parties or the application that use the legacy
keys will be ignored.

To address this issue, we introduced a
[Config Post Processor](https://github.com/laminas/laminas-zendframework-bridge/blob/master/src/ConfigPostProcessor.php)
for Mezzio applications and a [Configuration merge listener](https://github.com/laminas/laminas-zendframework-bridge/blob/master/src/Module.php)
for MVC applications.

Under the hood, each does the same thing: they intercept legacy configuration
keys and merge their values with the default configuration provided under the
new keys.

The result is that component configuration can reference the new keys:

```php
return [
    'mezzio-hal' => [
        'metadata-factories' => [
            ResourceMetadata::class => ResourceMetadataFactory::class,
        ],
    ],
];
```

and factories can reference only the new keys as well:

```php
class MetadataMapFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];

        $metadataMapConfig = $config[\Mezzio\Hal\MetadataMap::class] ?? [];
        $metadataFactories = $config['mezzio-hal']['metadata-factories'] ?? [];

        return new \Mezzio\Hal\MetadataMap($metadataMapConfig, $metadataFactories);
    }
}
```

When a configuration post processor is in play, third party configuration
referencing the old keys will have its own configuration merged under the new
keys, keeping backwards compatibility.

> While the configuration post processing works, it is highly reliant on the
> idea that application-specific configuration is merged last. As such, we
> recommend that third-party library providers update their libraries. Until
> then, however, the configuration post processors provide a solution that does
> not incur a BC break.

### Custom Request Attributes in Middleware

When using [PSR-15 middleware](https://www.php-fig.org/psr/psr-15/),
such as with Mezzio (formerly Expressive), we pass information between
middleware using _request attributes_. We have standardized on using class
names for these attributes, raising another migration problem. As an example,
consider the following routing middleware:

```php
class RouteMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $result = $this->router->match($request);

        $request = $request->withAttribute(\Zend\Expressive\Router\RouteResult::class, $result);

        return $handler->handle($request);
    }
}
```

This middleware injects an attribute containing the results of routing, so that
users can later access them. This also means that users are using the legacy
class name, `Zend\Expressive\Router\RouteResult`, in order to retrieve those
values.

To provide backwards compatibility, we decided to inject two attributes, one under
both the current class name, and one under the legacy class name:

```php
class RouteMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $result = $this->router->match($request);

        $request = $request
            ->withAttribute(\Mezzio\Router\RouteResult::class, $result)
            ->withAttribute(\Zend\Expressive\Router\RouteResult::class, $result);

        return $handler->handle($request);
    }
}
```

This allows pulling using either name, ensuring your code, or third-party code,
continues to work without changes.

> See the transfer tool code: [MiddlewareAttributesFixture](https://github.com/michalbundyra/laminas-transfer/blob/master/src/Fixture/MiddlewareAttributesFixture.php).

### Methods with "Zend" in the name

In several places, we have defined method names containing the word "Zend". We
provided a solution here mimicing what we did for namespaced functions: we
renamed the existing function using "Laminas" in the name, and then added a new
function using the old name that proxies to the original.
For an example, you can review the [`Psr7Response` class](https://github.com/laminas/laminas-psr7bridge/blob/master/src/Psr7Response.php#L99-L113).

> See the transfer tool code: [SourceFixture](https://github.com/michalbundyra/laminas-transfer/blob/master/src/Fixture/SourceFixture.php#L120-L157).

### Still not everything

The above sections detail the various common problems we encountered across
multiple repositories. Unfortunately, we encountered many edge cases, and ended
up with [custom rules for 30 components](https://github.com/michalbundyra/laminas-transfer/tree/master/src/Fixture/Custom).

One example worth noting: we needed to keep all references to the
[Zend Server](https://www.zend.com/products/zend-server) product, but change
references to the [zend-server](https://github.com/zendframework/zend-server)
component (which has nothing to do with the Zend Server product).
See the [`ZendServerDisk` class](https://github.com/laminas/laminas-cache/blob/master/src/Storage/Adapter/ZendServerDisk.php)
and [`ZendMonitor` class](https://github.com/laminas/laminas-log/blob/master/src/Writer/ZendMonitor.php)
for examples.

As another example, "Zend" or "Expressive" are used as subnamespace names in
several third-party libraries (see [`container-auryn`](https://github.com/mezzio/mezzio-skeleton/blob/master/src/MezzioInstaller/Resources/config/container-auryn.php#L5-L6)),
and we needed to ensure these were left unchanged.

## How we tested

Before we were able to launch, we needed to test that everything would work.
We tried the rewrite tool on multiple libraries, and they looked fine - but we
needed something more useful than looking over the code.

### Rewrite packages in `vendor`

The first approach we took was to rewrite all packages under the `vendor/`
directory of a ZF-based project. We added a command to our transfer tool to do
this, and had some immediate successes.The approach gave us confidence that what
we had written would likely work.

The problem, however, is that it didn't allow us to test every possible
component or combination. We needed something more robust.

> See the transfer tool code: [VendorCommand](https://github.com/michalbundyra/laminas-transfer/blob/master/src/Command/VendorCommand.php).

### Local testing

The second idea was to rewrite all components locally, create a local Composer
repository from them, add this repository to each component, and then install
dependencies and run tests for each.

This worked well, and gave us a lot of useful information. It helped us to find
some failing cases, but we felt it still was not sufficient: we were generally
testing only against one PHP version; we were only testing against the latest
versions of dependencies; there were many a variety of configuration issue, and
we were not able to run all tests due to missing dependencies, extensions,
and/or services (e.g., mongodb, database, swoole, ...).

> See the transfer tool code: [LocalTestCommand](https://github.com/michalbundyra/laminas-transfer/blob/master/src/Command/LocalTestCommand.php).

### Satis repository

The next thing we did was to rewrite all tags of all components we planned to
migrate, and use these to create a proper Composer repository using
[Satis](https://github.com/composer/satis) that we could expose publicly. This
would allow us to test any project or library against any version available.

At this point, we opened testing to the public, and asked the community to help.
We released [very first version of Laminas Migration Tool](https://github.com/laminas/laminas-migration/releases/tag/0.1.0)
and started testing ZF-based projects.

During this phase, we identified and resolved a number of edge cases we would
never have found otherwise.

But it was still not enough.

### Running unit tests on each component

As a final effort, we decided to do full continuous integration on each
component.

(You would probably think that we should have started from that, but you'd be
wrong! We couldn't take this step until we had a public Satis repository.)

To do this, we created test organisations for each of our projects,
pushed all components to these organisations, and enabled [Travis CI](https://travis-ci.com)
on each.

We modified the configuration to also run the php linter on the source
code, as our rewrite tool was heavily using regular expressions.
This helped identify some edge cases with rewriting, but also reported a lot
of false-positives. As one example, we have a number of classes that are enabled
only under specific PHP versions. We also have classes named after PHP keywords that
were later reserved; in those cases, we have replacements, but the linter would
flag the legacy classes as invalid.

We anticipated this phase would take quite some time, due to the fact that
Travis CI for open source limits the number of parallel operations that can be
run for any given account. Considering we were testing over 150 repositories on
each of PHP 5.6, 7.0, 7.1, 7.2, and 7.3 (and also some on 7.4!), each against
both lowest and latest dependencies, the number of jobs was enormous! In
reality, this went much faster than expected, but nevertheless, we were often
fixing issues during the day, and waiting for tests to run overnight.

This approach allowed us to identify a ton of issues, and we ended up rebuilding
repositories and our Satis instance multiple times during the process, until we
were satisfied with the results. In the end, the remaining issues we had were
cases of test expectations that needed to change due to renamed classes and/or
configuration, and a few minor ones that we were unable to reproduce anywhere
except on the Travis CI platform itself.

### Not everything was perfect

While we were constantly improving the tooling, we knew that it would never
likely be perfect, and we would have issues to resolve after the migration was
over.

For example we dropped Code Style checks in our tests as many of them were
failing due to line lengths.

We were not able to rewrite images. Regenerating all images just to keep
references to new libraries was not possible at this time, so we decided to
leave it as "post-migration" manual operation.

Nevertheless, we used the migration tooling as a chance to resolve a number of
long-standing issues. To list a few:

1. Alphabetising import statements (as namespaces were changed we,
   want to keep alphabetical order). We were able to do this via a [PHP Code
   Sniffer](https://github.com/webimpress/coding-standard/blob/master/src/WebimpressCodingStandard/Sniffs/Namespaces/AlphabeticallySortedUsesSniff.php)
   on rewritten PHP files.

2. In some old version of packages, we were using [grouped import statements](https://www.php.net/manual/en/language.namespaces.importing.php#language.namespaces.importing.group)
   so we ran [another CS fix](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.5.4/src/Standards/PSR2/Sniffs/Namespaces/UseDeclarationSniff.php) to split them.

3. Copyright headers. We have completely changed copyright headers in all
   of the files. Before, we kept the copyright year and it was
   inconsistently updated (we had some rules when the year should be updated, but
   very often we forgot to do it). Now we have a much simpler copyright
   header with references to other files in the repository (`LICENSE.md` and
   `COPYRIGHT.md`).

4. Updating spacing to follow [PSR-12](https://www.php-fig.org/psr/psr-12/). As
   the PSR-12 Coding Standard was already approved, we decided to add a blank
   line after the opening `<?php` tag so we wouldn't need to to it later on.

5. Multiple QA unification and improvements: Travis CI configuration,
   entries in `.gitattributes`, `.gitignore`, PHPUnit and PHPSpec
   configurations, etc. were all made consistent.

6. Documentation and its configuration unification. Some documentation was under
   the legacy `doc/` subdirectory, while others used `docs/` (which is a
   recommended path for GitHub community support documents as well). The
   `mkdocs.yml` file used to govern how documentation is rendered has also been
   updated over time. We used this as a chance to make these consistent across
   all repositories.

7. Consistent Github templates for Pull Requests and Issues. In fact, we ended
   up moving these into organization-level `.github` repositories, so they can
   be updated all at once, instead of having to update them across all
   repositories.

8. Updated all support files (e.g., `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`,
   `SUPPORT.md`).

## Finally: the transition

Finally, after over a year from the first announcement, and 10 months of
working on the [transfer tool](https://github.com/michalbundyra/laminas-transfer),
we decided we were ready to launch Laminas, and we chose to do so on the last
day of 2019.

The days and weeks before, normally holidays for most of us, were spent
polishing and prepping the tools for the transfer. We even had some "last
minute changes" which surprised us and prevented us from starting as early
as we wanted, but managed to migrate everything by around noon UTC time.

We knew not everything was perfect, and there was still a lot left to do, but
we'd managed to deliver what we'd been promising: we'd deprecated and archived
all Zend Framework repositories, and created all new components under three
brand new organisations: [Laminas](https://github.com/laminas),
[Mezzio](https://github.com/mezzio) and [Laminas API Tools](https://github.com/laminas-api-tools).

### We were right: not everything was perfect

Shortly after completing the migration, we received user reports of issues.

The first the most serious issue was with [namespaced function](#custom-functions).
We just missed "return" statement in legacy functions with the call to new functions.

Because of that we had to issue patch version for: 

- [laminas-diactoros](https://github.com/laminas/laminas-diactoros),
- [laminas-stratigility](https://github.com/laminas/laminas-stratigility),

We noticed also that some versions of [laminas-view](https://github.com/laminas/laminas-view)
had not been transferred correctly: all tags between 2.2.4 and 2.5.3 and we
have also issued patch version for these.

Also, [Laminas API Tool skeleton application](https://github.com/laminas-api-tools/api-tools-skeleton/)
had wrong module registered (due to rename from `ZendDeveloperTools` to `Laminas\DeveloperTools`),
and it had been patched.

> Patched version
>
> In above repositories we released new tags with `p1` suffix,
> as these took precedence during `composer update` operation.

And thankfully that was everything. We've had no blocking issues reported
since late January, while we continue to get reports of successful migrations.

In last two months, we've also seen many third party repositories migrate to
Laminas.

If you still have not updated your application or your company is still
using Zend Framework components, we recommend you migrate, so that you continue
to get security updates. Please see our [migration guide](https://docs.laminas.dev/migration/).

## Life after life. What next?

We'e had our [first Technical Steering Committee meeting](https://getlaminas.org/blog/2020-03-05-tsc-inaugural-meeting.html),
and started planning how we want to maintain and expand the project.

Please [follow us on Twitter](https://twitter.com/getlaminas) to not miss any updates,
[join our chat](http://dev.laminas.com/chat) and [visit our forum](https://discourse.laminas.dev) today!

Stay tuned!
