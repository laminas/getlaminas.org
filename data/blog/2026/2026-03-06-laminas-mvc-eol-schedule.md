---
id: 2026-03-06-laminas-mvc-eol-schedule
author: asgrim
title: 'Laminas MVC End of Life Schedule'
draft: false
public: true
created: '2026-03-06T09:00:00-00:00'
updated: '2026-03-06T09:00:00-00:00'
tags:
  - MVC
  - middleware
  - framework
  - software architecture
  - software lifecycle
openGraphImage: '2026-03-06-laminas-mvc-eol-schedule.png'
openGraphDescription: 'Laminas MVC End of Life Schedule'
---

We [previously announced](https://getlaminas.org/blog/2025-06-06-laminas-mvc-is-retiring.html) that **Laminas MVC**, and its related components would be entering `security-only` status until PHP 8.5 is released, around November 2025.

> This announcement only applies to the **Laminas MVC**.  
> The [Mezzio](https://docs.mezzio.dev/) and [Laminas components](https://docs.laminas.dev/components/) are still in active development.

<!--- EXTENDED -->

There is some good news in the latest Laminas Technical Steering Committee (TSC) meeting, it was agreed that **Laminas MVC** would continue with `security-only` status until the security support for PHP 8.4 ends, which will be 31st December 2028. This means that **Laminas MVC** will continue to receive security patches until that date, but no new features or bug fixes. After this date, there won't be any more releases. This affects the following packages:

* laminas-mvc
* laminas-developer-tools
* laminas-mvc-form
* laminas-mvc-i18n
* laminas-mvc-middleware
* laminas-mvc-plugin-fileprg
* laminas-mvc-plugin-flashmessenger
* laminas-mvc-plugin-identity
* laminas-mvc-plugin-prg
* laminas-mvc-plugins
* laminas-test
* laminas-mvc-skeleton
* laminas-modulemanager
* laminas-config-aggregator-modulemanager
* laminas-skeleton-installer
* laminas-composer-autoloading

## How did we get here?

**Laminas MVC**, and Zend Framework before it, has been used by countless companies across the globe. Whilst the decision to eventually discontinue **Laminas MVC** was not taken lightly, the TSC agreed that the best way forward was with the modern [Mezzio](https://github.com/mezzio/mezzio) ecosystem. Back in the [May 2025 TSC meeting](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2025-05-05-TSC-Minutes.md#conclusions), it was agreed that **Laminas MVC** should become `security-only`. Unfortunately, this means **Laminas MVC** will not be updated to support PHP 8.5

## The future is Mezzio

We've been encouraging people to move to Mezzio for a long time now, so here's another push that way. The more modern and flexible middleware approach of Mezzio has so many benefits. However, it is difficult to recommend a *one size fits all* approach that would work for every application, due to the lifecycle differences between how the MVC and Mezzio work. That said, we do recommend moving to Mezzio as the way forward, and there is [great community](https://getlaminas.org/participate/) on both our Discourse and Slack, who may be able to help with your migration, should you choose to carry this out. In addition, we plan to write up and publish some migration guides. Watch this space for these migration guides soon!

If you need additional support, there are several consultancies who could provide paid services to help your company. Please note that none of these companies are endorsed by the Laminas Project, and are in no particular order:

* [Roave](https://roave.com/) - Roave's best-in-class software architects & engineers help your team master complex technology challenges.
* [Perforce Zend](https://www.perforce.com/) - Develop high-stakes, mission-critical applications without compromise, from code to business ready, with Perforce.
* [Apidemia](https://www.apidemia.com/) - API and platform migration, PHP legacy software modernization.
* [Peptolab](https://www.peptolab.com/) - PeptoLab designs and builds websites, digital applications and bespoke business solutions.
* [Webinertia](https://webinertia.dev/) - We craft next-generation digital experiences that push boundaries and redefine what's possible in web development.

In addition, you can ask in the [Laminas Slack](https://laminas.slack.com/) or [Laminas Discourse](https://discourse.laminas.dev/) for recommendations on consultants. We wish you a happy migration to Mezzio!
