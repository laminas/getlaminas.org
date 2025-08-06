---
id: 2025-08-06-strangler-fig-pattern
author: bidi
title: 'The Strangler Fig Pattern: A Viable Approach for Migrating MVC to Middleware'
draft: false
public: true
created: '2025-08-06T11:00:00-01:00'
updated: '2025-08-06T11:00:00-01:00'
tags:
  - migration
  - MVC
  - middleware
openGraphImage: '2025-08-06-strangler-fig-pattern.png'
openGraphDescription: 'The Strangler Fig Pattern: A Viable Approach for Migrating MVC to Middleware'
---

The Strangler Fig Pattern is a gradual migration technique that allows you to progressively replace components from an existing legacy system with a new, modern platform.
This is done while both old and new code coexist and operate in parallel during the transition.

<!--- EXTENDED -->

In a nutshell, the old platform is put behind an intermediary interface.
During development, as the new services are coded, they replace old components behind the scenes.
As the execution is redirected through the new code, the old code can be removed completely until the whole system is migrated to the new architecture.

## When to Use the Strangler Fig Pattern?

**Rewriting the codebase** is always an option, but **is often avoided** in favor of the progressive approach as we will discuss later in this article.
The recent sunsetting of [**Laminas MVC**](https://docs.laminas.dev/mvc/) provides the perfect example for when to consider migration.

So you have a legacy system like Laminas MVC built with older architecture.
It was good a decade ago, but has now reached a point where **maintaining it is a chore** that even skilled, well-paid developers shy away from.
Implementing a new feature is a difficult undertaking which may even be impossible because of the system's limitations when trying to interface with modern dependencies.

The **solution** is obvious when you consider that modern packages work fine with modern platforms and are more easily integrated into them, so this is the moment when the CTO must decide how to proceed.
If you used Laminas MVC in your platform, the natural progression is toward the Laminas alterative: the [**Mezzio microframework**](https://docs.mezzio.dev/) and its more opinionated implementation, the [**Dotkernel Headless Platform**](https://www.dotkernel.com/).

Migrating between Laminas MVC and Mezzio is like methodically replacing bricks in a wall.
The wall remains upright, even when a couple of crumbling bricks are being replaced with shiny, new bricks.
Bricks made of gold (OK, that was a bit much).
A well-implemented Strangler Fig Pattern will have that wall provide the same functionality throughout its rebuilding process.
At the end of the migration, the whole wall will contain all-new bricks, built better than before, anxiously awaiting new features.

## Why Not Rewrite the Old Platform Instead?

Many organizations are weighed down with legacy applications that:

- Are a vital business component that can't be put on pause during additional development.
- Are difficult to change or scale because of platform limitations or sheer complexity.
- Use outdated or discontinued technology stacks.

One of the options for companies that need to improve their workflow is a **complete rewrite**, but it carries several **disadvantages**:

- There is a risk of not migrating important domain logic and business logic that is not well documented.
- It's expensive and time-consuming to rewrite code that has been under constant improvement over many years.
- Likely to fail, especially because of learning curves and hiring challenges for developers of the new architecture.
- It's often rejected by stakeholders because of no perceived benefits or short-term value.

## What Are the advantages of the Strangler Fig Pattern?

The **Strangler Fig Pattern** offers a **safer, controlled way** to:

- **Incremental modernization** based on a detailed plan.
- **Faster delivery of features** with short coding sprints, aimed at replacing isolated components.
- **Improved maintainability** for each new component because of its decoupled state, which enables separate testing.
- **Flexible development pacing** where you set your goals in terms of development schedule (the process is long and can be interrupted more easily) and allocated resources (the developers who actually work on the migration).
- **Mitigate risks** by avoiding a full rewrite in favor of small, manageable changes.

## How Strangler Fig Pattern Works: Step-by-Step

### Step 1: Understand and Isolate Functionality

Analyze the legacy system to identify well-defined boundaries or modules.
Select a coherent part of the functionality that can be isolated and replaced independently (e.g. user authentication, payment processing).
It's a good idea to work with the original developers for this step, if they are still available.

### Step 2: Build the Replacement Code

Implement the isolated functionality in the new platform using modern architectures (e.g. microservices, middleware).
Ensure that the new service meets all the functional and non-functional requirements (performance, security etc.).

### Step 3: Intercept Calls Via the Routing Layer

Use a routing mechanism (like an API gateway, proxy, service mesh) to redirect calls that use that functionality to the new service.
This allows the rest of the application to continue working as-is while the new component handles specific operations.

### Step 4: Test and Monitor

Thoroughly test the new service in production (with e.g. canary releases, A/B testing).
Monitor logs, performance metrics and error rates to validate correctness and stability.

### Step 5: Repeat

Repeat the process from steps 1-4 for each component of the legacy system that needs to be replaced.
Each new component replaces a legacy component, meaning the latter is not used and can be removed.

### Step 6: Retire the Legacy System

Once all functionality is handled by the new services, the legacy system becomes obsolete.
You can fully decommission and remove the old system.

## Example in Practice

Consider this scenario:

You have a monolithic e-commerce application built with Laminas MVC.
It has several functional modules:

- `/products` - Product listings `/product/list`, editing `/product/edit` and deleting  `/product/delete`
- `/cart` - Cart management
- `/checkout` - Payments and orders
- `/account` - User account management

You want to migrate to a middleware-based architecture using Mezzio microframewok with Laminas components.

Let's follow the Strangler Fig Pattern in action:

- **Install the Mezzio microframework skeleton**: This is where the new code will be written as microservices.
- **Select a feature**: Start with `/product/list`, a non-critical module that is easy to isolate.
- **Build a new microservice**: Implement microservices for product listing, editing and deleting in the middleware platform.
- **Intercept requests**: Use an API gateway to route `/product/list` calls to the new microservice.
- **Test and monitor**: Gradually shift traffic from the monolith to the microservice.
- **Continue migrating**: Repeat the process for each feature like `/product/list`, `/cart` etc., one at a time.
- **Turn off the monolith**: Once all features are replaced, shut down the legacy application.

## Conclusion

The Strangler Fig Pattern is not a silver bullet, but it's one of the most pragmatic, low-risk approaches to modernizing legacy systems.
It’s particularly useful in enterprise software, where rewriting from scratch could take years and millions of dollars.

## Additional Resources

[Strangler Fig](https://martinfowler.com/bliki/StranglerFigApplication.html)
