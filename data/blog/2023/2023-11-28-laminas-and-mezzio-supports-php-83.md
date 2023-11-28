---
id: 2023-11-28-laminas-and-mezzio-supports-php-83
author: frank
title: 'Laminas and Mezzio Supports PHP 8.3'
draft: false
public: true
created: '2023-11-28T11:00:00-01:00'
updated: '2023-11-28T11:00:00-01:00'
tags:
    - laminas
    - mezzio
    - php
---

Version 8.3 of PHP was released on 23 November 2023, with [nice improvements and new features](https://www.php.net/releases/8.3/) such as typed class constants and the new function `json_validate`.

The support for version 8.3 was added to the components of Laminas and Mezzio.

<!--- EXTENDED -->

### Package Upgrades

With the publication of the first release candidate of version 8.3, we began preparing updates to ensure reliable results.
We began with foundational components, as these form the basis for major packages such as [laminas-mvc](https://docs.laminas.dev/laminas-mvc/).

In addition to the update to PHP 8.3, improvements are also being made to type inference for better results on static analysis with Psalm, and to support the latest PHPUnit version.

### Assistance

Just as in previous years, the upgrades were realised with the help of the community and, above all, projects that use Laminas components themselves.

The [Adobe Magento Open Source project](https://github.com/magento/magento2/) relies heavily on Laminas, and they provided a large number of pull requests to assist in PHP 8.3 support. But we also welcomed contributions from individuals and other projects, such as from the [Silverstripe CMS](https://www.silverstripe.org) project.

With the help of our maintainers and technical advisory team, support for PHP 8.3 was added to almost all components in a short space of time.

_The rest will follow shortly; check the task-boards on GitHub:_

- [GitHub project for components and laminas-mvc](https://github.com/orgs/laminas/projects/35)
- [GitHub project for Mezzio](https://github.com/orgs/mezzio/projects/7)

### Security-Only Maintenance Mode

A number of components are marked as feature complete and therefore only receive security updates. However, these components also received an upgrade to the latest PHP version, as they are still frequently used.

### Continuous Integration GitHub Action

With the help of [our own GitHub action](https://github.com/marketplace/actions/laminas-continuous-integration), we ensure the quality and functionality of supported PHP versions in each repository. Based on the version constraints in the Composer configuration of the package, unit and integration tests are performed for the individual PHP versions. In addition, static analyses are also performed via Psalm, the code styling is checked and the formatting of the related documentation is checked.

[GitHub Action: Laminas Continuous Integration](https://github.com/marketplace/actions/laminas-continuous-integration){.btn .btn-secondary .text-white}

### Version Policy

Our version policy includes support [for all active PHP versions](https://www.php.net/supported-versions), including the version with security support. This means that as of today, we now only support versions 8.1, 8.2, and 8.3.

---

_We would like to thank all helpers, contributors and team members!_
