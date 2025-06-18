---
id: 2024-07-02-dotkernel-a-replacement-for-laminas-api-tools
author: julian
title: 'Dotkernel API: a replacement for legacy Laminas API Tools'
draft: false
public: true
created: '2024-07-02T11:00:00-01:00'
updated: '2024-07-02T11:00:00-01:00'
tags:
  - laminas api tools
---

The Technical Steering Committee for the Laminas Project has decided to mark **Laminas API Tools (formerly Apigility)**
as `security-only`, indicating that no new features or enhancements will be developed and only security-related updates
will be provided going forward. This decision is made in light of the evolving landscape of web API development and the
desire to ensure that users have access to modern, fully supported tools.

<!--- EXTENDED -->

As an alternative, the committee recommends
**Dotkernel API**, a robust and actively maintained framework that offers enhanced features, better performance, and
ongoing support to meet the needs of developers. This recommendation aims to facilitate a smooth transition for users
seeking a reliable and future-proof solution for their API development needs.

### The issue with Laminas Api Tools

The **Laminas Technical Steering Committee** (or TSC)  decided in an initial meeting on **January 2023**, to mark
laminas-api-tools as `security-only`, as well as its [packages](https://github.com/orgs/laminas-api-tools/repositories)
as `security-only`, primarily because the resources to continue the project were not available.

[Extended discussion about Laminas Api Tools retirement](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2023-01-09-TSC-Minutes.md)

### How to move forward? Choose Dotkernel API

The TSC recommendation as a replacement for current Laminas API Tools is [Dotkernel API](https://www.dotkernel.org/),
mostly because it is built on top of Mezzio and uses Laminas components.

This architectural decision on **Dotkernel organization's** part ensures that the **Dotkernel API** offers the same
stability and forward-thinking that Laminas///Mezzio has employed in all its endeavors.

There is **no upgrade path** from Laminas API Tools to Dotkernel API, because of the many differences between the two.
You can see the [comparison table](https://www.dotkernel.com/dotkernel-api/dotkernel-api-versus-laminas-api-tools/) for
more details. Of note are the PHP version and the architecture.

### Why Dotkernel API is a good replacement for Laminas Api Tools

- **Dotkernel organization** has kept its ear to the ground over the past **6 years** to keep track of current coding
  trends, with focus on API projects, while also updating our packages to the **latest PHP version**, now 8.3.
- The MVC architecture of laminas-api-tools is replaced with a **Middleware architecture** (defined in PSR-7) -
  **Mezzio** in this particular case - which provides functionality to connect applications, tools and databases in an
  intelligent and efficient way.
- [The PHP Framework Interop Group](https://www.php-fig.org/) aims to establish a standardization and interoperability
  of programming concepts in PHP via a list of **PSRs**. Dotkernel API has already implemented PSR-3, PSR-4, PSR-7,
  PSR-11 and PSR-15.
- The **sunset header evolution strategy** was chosen as a way to highlight deprecation before an API endpoint
  (or feature) is updated, removed or replaced. It provides a clear alert, with a link to documentation, as well as
  ample opportunity for developers to implement the update.

### What is coming in the future

Dotkernel API has a lengthy **roadmap** that takes advantage of the latest architecture trends and active tools to
ensure that your application will pass the test of time.

- Implement **OpenAPI** to enable developers to discover and understand the capabilities of the API even without access
  to the source code or documentation.
- Implement a full **create-project procedure** for faster installation and configuration.
- **Expand the documentation** to help developers get their APIs up and running as fast as possible.
- Propose a **smart architecture for branching out** from a monolith API codebase to admin, frontend and macroservices.

### About Dotkernel API

The [**Dotkernel organization**](https://www.dotkernel.com) is actively involved in building and maintaining an open
source ecosystem with Mezzio and
Laminas Project as a solid and future-proof foundation.

**Dotkernel API** aspires to be a tool for the future aimed at intermediate-to-advanced level programmers, with
long-term support that guarantees it will be kept up-to-date on the latest trends.

The Dotkernel organization monitors the code regularly to check for bugs and apply improvements to create a stable,
fast, reliable solution for any project, from entry-level to enterprise.

- Website: [dotkernel.org](https://www.dotkernel.org/).
- Documentation for the API and all components and libraries: [docs.dotkernel.org](https://docs.dotkernel.org/).

[You can find the minutes in the TSC repository.](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2024-05-06-TSC-Minutes.md)
