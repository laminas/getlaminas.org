---
id: 2024-11-18-summary-of-the-meeting-in-november-2024
author: julian
title: 'Summary of the meeting in November 2024'
draft: false
public: true
created: '2024-11-18T11:00:00-01:00'
updated: '2024-11-18T11:00:00-01:00'
tags:
    - meeting summary
---

On Monday, 4 November 2024, the Technical Steering Committee for the Laminas Project held its monthly meeting. The topics discussed included:

- Abandoning several Laminas packages.
- Financing of the book [**Mezzio Essentials**](https://mezzioessentials.com/).

<!--- EXTENDED -->
### Abandoning several Laminas packages

The ever-changing PHP ecosystem means that a package must receive regular attention due to updates in the PHP language and its 3rd-party dependencies.
The most vulnerable to these changes are the legacy packages.
Eventually, it becomes too costly to maintain them.
The best solution becomes to abandon them, mark them archived and recommend viable alternatives for the systems that still use them.

#### Abandoning/archiving Laminas Oauth

The Laminas Technical Steering Committee (TSC) members first considered Laminas Oauth (`laminas/laminas-oauth`) for archival.
There are no resources for its maintenance and actively-maintained alternatives are available (Oauth 2 and OIDC).
The members swiftly decided to archive laminas-oauth with a poll on the day following the TSC meeting.

#### Abandoning/archiving legacy libraries

Several other legacy libraries were also mentioned as eligible to be archived or abandoned:

- `laminas-barcode`
- `laminas-config`
- `laminas-config-aggregator-modulemanager`
- `laminas-dom`
- `laminas-file`
- `laminas-http`
- `laminas-json`
- `laminas-loader`
- `laminas-log`
- `laminas-math`
- `laminas-memory`
- `laminas-paginator-adapter-laminasdb`
- `laminas-progressbar`
- `laminas-tag`
- `laminas-text`
- `laminas-uri`
- `laminas-xml`
- `laminas-xml2json`
- `laminas-zendframework-bridge`

Most likely, **laminas-http**, **laminas-uri** and **laminas-xml** will be left active.
Still, every package will have its own poll available for voting by the TSC members.
This topic will be revisited on the following TSC meeting to review the poll results and act upon them.

### Financing of the Book "Mezzio Essentials"

The book "Mezzio Essentials" has been welcomed by the PHP community as being essential reading to understand microframeworks, middlewares, PSR's, Mezzio's core components and much more.
The book contains both theoretical knowledge and a lengthly practical example, a step by step guide for building an application, first manually, then by using the Mezzio Skeleton Installer.
While it doesn't claim to be exhaustive, this book is a reliable starting point for the theoretical and practical understanding of using Mezzio Framework.

The book needs a thorough review and updates to take advantage of the current state of the Mezzio Framework.
The TSC members considered financing this effort.
More details must first be discussed with the book's author, Matthew Setter, before the topic is discussed further in the following TSC meeting.

#### Summary

While maintaining a long and encompassing list of packages is desirable, the cost of maintaining them must also be considered.
Other developer teams create similar packages and have more resources to keep them up to date, which makes them a better choice for their less-used Laminas alternatives.
The TSC members are carefully considering their many packages and pruning the ones not worth keeping active.

All developers can benefit from a well-written book on a given framework.
The TSC members understand this well and are willing to put the Laminas organization's funds to help create this beneficial tool for all developers who use the Mezzio Framework.

This TSC meeting didn't see a lot of definitive decisions, but the importance of the topics justifies the extra time dedicated to ensuring any decision is sound.

### Other News

#### Laminas Validator Version 3 Released

We [released version 3](https://github.com/laminas/laminas-validator/releases/tag/3.0.0) of `laminas-validator` last month with nearly 100 PR's merged.

Support for version 3 in other components is still in the works. The following libraries still need major releases to gain compatibility with validator v3 and in most cases, Service Manager v4:

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

#### Laminas Filter Version 3

Work on the next major version of `laminas-filter` is progressing well.
We've been modernising and refactoring all the filters so that they are largely immutable, improving type safety and predictability.

When it's released `laminas-filter` will support Laminas Service Manager v4 and unlocks continued work on the many dependent libraries, most importantly Laminas Input Filter which will be next in line.

We welcome contributions to help us get Laminas Filter over the line and there is a [handy todo list](https://github.com/laminas/laminas-filter/issues/177) you can consult to see if there's anything you can help with.

#### Getting Ready for PHP 8.4

Most Laminas and Mezzio components are now 8.4 compatible, ahead of the PHP 8.4 general release.

You can find the status of PHP 8.4 support in these projects:

- [8.4 Support in Mezzio](https://github.com/orgs/mezzio/projects/8)
- [8.4 Support in Laminas](https://github.com/orgs/laminas/projects/37)

As always, PRs are welcome 👍
