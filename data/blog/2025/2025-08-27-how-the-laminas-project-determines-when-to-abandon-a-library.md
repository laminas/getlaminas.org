---
id: 2025-08-27-how-the-laminas-project-determines-when-to-abandon-a-library
author: bidi
title: 'How the Laminas Project Determines When to Abandon a Library'
draft: false
public: true
created: '2025-08-27T11:00:00-01:00'
updated: '2025-08-27T11:00:00-01:00'
tags:
  - software lifecycle
  - open source
  - maintainability
  - abandoning code
  - middleware
openGraphImage: '2025-08-27-how-the-laminas-project-determines-when-to-abandon-a-library.png'
openGraphDescription: 'How the Laminas Project Determines When to Abandon a Library'
---

The world of open source coding is an ever-changing one.
Software rises and falls in popularity and usage based on a multitude of factors.
In this article we will explore the reasons why software becomes abandoned, from active decisions, to lack of usage.

<!--- EXTENDED -->

Some of the factors that impact software lifecycle are:

- Number of features.
- User experience.
- Maintenance schedule.

Buzzwords are sometimes responsible for the popularity of software going up, but if the developers don't have the resources to keep the ball rolling, the users can get unhappy.
Closed source software is one thing, but the open source scene is not far behind in pushing the developers to make impactful decisions related to the application.

- As software grows, you may find that the only way to properly move forward is to rewrite the code in another framework or risk hitting bottlenecks or even insurmountable obstacles.
- Other times, the best solution is to abandon your implementation in favor of another, because it has a more active community or a more future-proof architecture.
- Sometimes the problem your solution addressed disappears or is no longer relevant, and there is no point in continued maintenance.

Moving forward in this article, we will focus mostly on PHP packages, but the discussion may as well apply to other forms of software.

## Reasons for abandoning code

Often, the functionality provided by a package is going to be mirrored by one or more alternatives.
This is because the teams that developed it might have used the package in their closed source software and have decided to release it to the open source ecosystem.
In open source, more eyes are going to look at the code and recommend improvements.
Sure, there may be criticism, but there are good reasons for it:

- It's constructive which leads to improvements.
- It helps find and remove bugs in the code.

### Some packages lose popularity organically

Eventually, the 'winner' of the makeshift popularity contest will push developers of the less popular package to consider abandoning their implementation.
If it lacks popularity, then the impact is minimal and the decision to abandon the code is easier on the owners.

### Fully-functional packages are not immune to being abandoned

Even if the code is perfectly functional, when the developers move on to other projects, they find they can't allocate more time and manpower to maintain legacy code.
The code may be passed onto others who want to keep the project alive.

We all know that using abandoned packages, even when dealing with feature-complete code, is sometimes not the best option, compared to the alternative that has developers still actively engaged in maintaining it.
In software, the lack of updates can be a cause for worry that the project is abandoned, even if not stated explicitly.

### Bad long-term planning leads to technical debt

No matter how well the architecture is defined at first, a constant development process may lead to unwanted shortcuts that slowly build on technical debt.
Eventually, implementing a new feature means rewriting too much code or adding potential instability.

### Keep the buzz going or people forget about you

The original expression is 'strike while the iron is hot', but in our case, the 'iron' (software) remains hot only when it is struck (maintained).

Since there is often indirect competition between frameworks and packages, some will continually gain popularity and followers, while others naturally fade into anonymity.
The go-to option will too often be the more popular package with an active community and a pretty marketing campaign, while the less popular one stagnates on feature implementation.
Marketing takes many forms:

- The professional (and expensive) approach.
- Keeping the community engaged with:
    - Regular updates,
    - New features,
    - Discussions,
    - Articles and the like.

## Reasons to keep the code up-to-date

Abandoning code is easy, but there are reasons to keep code alive and well-maintained.

Widespread usage often guarantees a large community supporting a package with contribution in both code and donations.
Donations to an open source project may not help the developer retire any time soon, but it can help in the short term to keep the package running well.
Obtaining a more reliable source of donations from the private or government sector does certainly go a long way toward keeping devs focused.

## How Laminas handles the code lifecycle

Over the past year, the Laminas Technical Steering Committee has been reviewing their long list of packages in order to decide what to abandon and what to keep active.
During the [TSC meeting on 2024-11-04](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2024-11-04-TSC-Minutes.md), several packages in security-mode were discussed and the ultimate decision was to mark most of them discontinued.

A similar decision was made for [Laminas MVC](https://github.com/laminas/laminas-mvc), though the decision is much more impactful.
There are many projects that still use Laminas MVC and are actively supported by teams that still receive feature requests.
Most of the recent development effort from the Laminas members has shifted to [Laminas ServiceManager 4](https://github.com/laminas/laminas-servicemanager) (which implements [PSR-11 Container version 2](https://github.com/php-fig/container)) and the middleware architecture.
For this reason, it's unreasonable to keep Laminas MVC up to date which requires a great deal of attention from TSC members who simply have no more time for it.
Archiving Laminas MVC will be a slow process to make sure that its users have plenty of time to move to an alternative, like the recommended middleware architecture.

### Additional resources

- [TSC Minutes 2024-11-04](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2024-11-04-TSC-Minutes.md)
- [TSC Minutes 2025-05-05](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2025-05-05-TSC-Minutes.md)
- [Laminas MVC](https://github.com/laminas/laminas-mvc)
- [Laminas MVC Is Retiring](https://getlaminas.org/blog/2025-06-06-laminas-mvc-is-retiring.html)
- [Laminas Service Manager](https://github.com/laminas/laminas-servicemanager)
