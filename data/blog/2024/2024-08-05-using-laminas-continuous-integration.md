---
id: 2024-08-05-using-laminas-continuous-integration.md
author: julian
title: 'Using Laminas Continuous Integration'
draft: false
public: true
created: '2024-08-05T11:00:00-01:00'
updated: '2024-08-05T11:00:00-01:00'
tags:
  - continuous integration
---

**Continuous Integration (CI)** involves the frequent **merging of code updates** from developers into a shared
codebase.
This can be done as often as those updates are ready to be reviewed. Once the CI process is finalized,
**Continuous Delivery** automatically **deploys the code** to the production environment.
This flow streamlines the development process for faster and more reliable releases.

<!--- EXTENDED -->

If you only have a handful of codebases, you may be able to handle their maintenance manually, but the automated
solution is just around the corner. Take the [Laminas Project](https://github.com/laminas), for example.

It has around 200 repositories between the main project and Mezzio. Maintaining all of that code, from bug fixes to
patches, from improvements to releases is a titanic task. Laminas CI is designed to help maintainers and developers
review, merge and release updates rapidly and with fewer headaches. Wouldn't you rather sleep soundly at night knowing
that your code has been checked by a tireless, objective reviewer? Laminas CD will be the topic of a future article, so
let's focus on Laminas CI.

### Can I use Laminas CI in my own projects?

Of course! Any project or package can benefit from using CI, especially Laminas CI which is designed to be universal
and easy to install.
You can grab it from [**Github Marketplace**](https://github.com/marketplace/actions/laminas-continuous-integration).

We will cover the implementation in the next chapter. **DotKernel API** is one of the many organizations that use
Laminas
components, so it makes perfect sense to trigger Laminas CI for each code update, as can be
seen [here](https://github.com/dotkernel/api/tree/5.0/.github/workflows).

### How to implement the Laminas CI pipeline

A generic approach to CI is to use GitLab CI/CD, a built-in continuous integration, delivery, and deployment feature of
GitLab that allows you to configure your own pipeline of jobs, stages and scripts. This still means that you have to
know what you are doing, so an excellent choice is the Laminas CI implementation which does most of the heavy lifting
for you. It generates a matrix of jobs for Git Actions based on your project's configuration, then runs the jobs and
provides feedback.

The first file you need to create is `.github/workflows/continuous-integration.yml` using the content below. It needs to
be used in the GitHub Events that trigger workflows. You can check out the
documentation [here](https://docs.github.com/en/actions/using-workflows/about-workflows).

```yaml
name: "Continuous Integration"

on:
  pull_request:
  push:
    branches:
    tags:

jobs:
  ci:
    uses: laminas/workflow-continuous-integration/.github/workflows/continuous-integration.yml@1.x
```

In DotKernel, when pushing PHP code for review, Laminas CI generates a series of automated processes which are designed
to:

- Check **linting** using phpcs based on phpcs.xml (phpcs.xml.dist can also be used).
- Run the battery of **tests** using PHPUnit based on phpunit.xml (phpunit.xml.dist can also be used).
- Run **static analysis** to detect bugs and check that best programming practices are respected using psalm based on
  psalm.xml (psalm.xml.dist can also be used).
- Run **yamllint and markdownlint** for `docs?/book/**/*.md` files and mkdocs.yml.

A full list of its capabilities is listed [here](https://github.com/laminas/laminas-ci-matrix-action). Check it out to
see what items it can identify based on the configuration files you include in your project.

Based on your project and the files you update in a given push, you can expect to see a **job matrix** like the one
below:

- Documentation Linting [8.2, latest]
- MkDocs Linting [8.2, latest]
- PHPUnit [8.2, lowest]
- PHPUnit [8.2, latest]
- PHPUnit [8.3, lowest]
- PHPUnit [8.3, latest]
- PHPCodeSniffer [8.2, latest]
- Psalm [8.2, latest]

Note that the job matrix generator is smart enough to ignore jobs that are not relevant to the affected items in your
update. PHPCodeSniffer may be missing if you only updated .md files and only relevant PHP versions will be used by
PHPUnit. This ensures that the job matrix is as effective and efficient as possible, while still offering a solid
review.

Each of the above jobs takes its **configuration settings** straight from your project:

- **PHP version**
- **Additional extensions**
- **php.ini settings**
- **dependency** - lowest, locked, or latest

Once the job has the above values, it **runs a command** which is the actual QA check.

The automated process returns a result for each job. If you get any errors, the job is marked as failed. You may be able
to fix some of the errors automatically in your local code e.g. via phpcs and psalm specific commands (phpcbf and
psalter, respectively) while others need to be investigated manually. Only when all errors are resolved do you get the
coveted green check mark. And a good night's sleep, of course.

### Conclusions

Laminas CI in a CI pipeline can be advantageous, especially for projects built with the Laminas//Mezzio framework, but
also for any other framework, given its broad range.

- **The CI pipeline can be optimized for PHP-specific tasks** because Laminas is designed specifically for PHP
  applications. The tasks range from dependency management with Composer, to code style checks, to running PHPUnit
  tests.
- Seamless **integration with Common CI/CD tools** like GitLab CI/CD, GitHub Actions, Jenkins and others.
- **Composer integration** ensures consistent dependency management across different environments, from development to
  production, which is crucial for maintaining stability and reliability.
- Laminas encourages **modern PHP practices** by enforcing PSR (PHP Standard Recommendation) compliance and the use of
  dependency injection.
- Extensive **Documentation and Community Support**

### More on the topic:

- [https://mwop.net/blog/2021-03-12-laminas-ci.html](https://getlaminas.org/blog/2023-11-28-laminas-and-mezzio-supports-php-83.html)
- [https://github.com/laminas/laminas-continuous-integration-action](https://getlaminas.org/blog/2023-11-28-laminas-and-mezzio-supports-php-83.html)
- [https://getlaminas.org/blog/2023-11-28-laminas-and-mezzio-supports-php-83.html](https://getlaminas.org/blog/2023-11-28-laminas-and-mezzio-supports-php-83.html)