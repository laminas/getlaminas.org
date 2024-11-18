---
id: 2024-11-18-october-development-news
author: george
title: 'October 2024 Development News'
draft: false
public: true
created: '2024-11-18T11:00:00-01:00'
updated: '2024-11-18T11:00:00-01:00'
tags:
    - development news
---

## Laminas Validator Version 3 Released

We [released version 3](https://github.com/laminas/laminas-validator/releases/tag/3.0.0) of `laminas-validator` in October with nearly 100 PR's merged.

Support for version 3 in other components is still in the works. The following libraries still need major releases to gain compatibility with validator v3 _(and in most cases, Service Manager v4)_:

- `laminas-form`
- `laminas-i18n`
- `laminas-inputfilter`
- `laminas-i18n-phone-number`
- `laminas-i18n-mvc`
- `laminas-i18n-view`
- `laminas-navigation`
- `laminas-session`
- `laminas-authentication`
- and more…

Please do try out the new version where possible and let us know how you get on in Slack, and any help you can provide with working on the dependents is appreciated!

## Laminas Filter Version 3

Work on the [next major version](https://github.com/laminas/laminas-filter/milestone/4) of `laminas-filter` is progressing well.
We've been modernising and refactoring all the filters so that they are largely immutable, improving type safety and predictability with over 50 PRs merged so far.

When it's released `laminas-filter` will support Laminas Service Manager v4 and unlocks continued work on the many dependent libraries, most importantly Laminas Input Filter which will be next in line.

We welcome contributions to help us get Laminas Filter over the line and there is a [handy todo list](https://github.com/laminas/laminas-filter/issues/177) you can consult to see if there's anything you can help with.

## Getting Ready for PHP 8.4

Most Laminas and Mezzio components are now 8.4 compatible, ahead of the PHP 8.4 general release.

You can find the status of PHP 8.4 support in these projects:

- [8.4 Support in Mezzio](https://github.com/orgs/mezzio/projects/8)
- [8.4 Support in Laminas](https://github.com/orgs/laminas/projects/37)

As always, PRs are welcome 👍
