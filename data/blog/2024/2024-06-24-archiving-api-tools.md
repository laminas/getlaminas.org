---
id: 2024-06-24-archiving-api-tools
author: julian
title: 'Laminas API Tools (formerly Apigility) retirement'
draft: false
public: true
created: '2024-06-24T11:00:00-01:00'
updated: '2024-06-24T11:00:00-01:00'
tags:
  - meeting summary
---

On Monday, **6 May 2024**, the Technical Steering Committee for the Laminas Project held its monthly meeting.
The topics discussed included the **Laminas API Tools (formerly Apigility)** alternatives.

<!--- EXTENDED -->

### Api Tools discussion

The **Laminas Technical Steering Committee** decided in an initial meeting on **January 2023**, to mark
laminas-api-tools as security only. The main reason is that laminas-api-tools main architecture is MVC and this
architectural pattern is almost dead in favor of Middleware pattern (Mezzio). A new version of laminas-api-tools would
be a new tool, without a migration path from current to the new one.

All [laminas-api-tools packages](https://github.com/orgs/laminas-api-tools/repositories) will be marked as `abandoned`.

[Extended discussion about Laminas Api Tools retirement](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2023-01-09-TSC-Minutes.md)

### Dotkernel API

Dotkernel API, because it is built on top of Mezzio and use Laminas components, was proposed as a recommendation or a
replacement for current API Tools projects.

- There is a new dedicated website only for Dotkernel API, which is the
  focus [dotkernel.org](https://www.dotkernel.org/).
- There is now compiled, up-to-date documentation, for the API and all components and
  libraries: [docs.dotkernel.org](https://docs.dotkernel.org/).
- It has integrated the [Laminas CI](https://github.com/marketplace/actions/laminas-continuous-integration) in all
  components and libraries.

[You can find the minutes in the TSC repository.](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2024-05-06-TSC-Minutes.md)
