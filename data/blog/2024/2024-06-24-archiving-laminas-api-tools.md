---
id: 2024-06-24-archiving-laminas-api-tools
author: julian
title: 'Laminas API Tools (formerly Apigility) retirement'
draft: false
public: true
created: '2024-06-24T11:00:00-01:00'
updated: '2024-06-24T11:00:00-01:00'
tags:
  - laminas api tools
---

On Monday, **6 May 2024**, the Technical Steering Committee for the Laminas Project held its monthly meeting.
The topics discussed included the **Laminas API Tools (formerly Apigility)** alternatives.

<!--- EXTENDED -->

### Api Tools discussion

The **Laminas Technical Steering Committee** decided in an initial meeting on **January 2023**, to mark
laminas-api-tools as `security-only`.

The main reasons for that decisions is that even the users want us to continue maintaining it , but we do not have
resources to do so.

All [laminas-api-tools packages](https://github.com/orgs/laminas-api-tools/repositories) will be marked as
`security-only`.

[Extended discussion about Laminas Api Tools retirement](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2023-01-09-TSC-Minutes.md)

### Recommendation

The Technical Steering Committee for the Laminas Project recommend as replacement for current Laminas API Tools
[Dotkernel API](https://www.dotkernel.org/), mostly because it is built on top of Mezzio and use Laminas components.

There is no upgrade path from Laminas API Tools to Dotkernel API.

#### About Dotkernel API

- Website:  [dotkernel.org](https://www.dotkernel.org/).
- Documentation for the API and all components and libraries: [docs.dotkernel.org](https://docs.dotkernel.org/).

[You can find the minutes in the TSC repository.](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2024-05-06-TSC-Minutes.md)
