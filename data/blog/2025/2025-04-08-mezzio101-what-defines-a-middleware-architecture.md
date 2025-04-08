---
id: 2025-04-08-mezzio101-what-defines-a-middleware-architecture
author: bidi
title: 'Mezzio101: What Defines a Middleware Architecture?'
draft: false
public: true
created: '2025-04-08T11:00:00-01:00'
updated: '2025-04-08T11:00:00-01:00'
tags:
  - middleware
  - software architecture
  - framework
openGraphImage: '2025-04-08-mezzio101-what-defines-a-middleware-architecture.png'
openGraphDescription: 'Mezzio101: What Defines a Middleware Architecture?'
---

Nowadays, applications are getting more and more complex.
The classic way of executing sequential code, with a single PHP script that accepts a request and outputs the information like we used to do in the PHP4 days, still works for simple apps, but using a modern architecture has many benefits.
Enter **middleware**: intermediary code that processes the request and/or response in various ways before it even reaches your custom code.

<!--- EXTENDED -->

In this article we will list the advantages of using middleware in your applications.
From maintainability, to future-proof design, the benefits can't be ignored.

### What does middleware do?

The versatility of middleware is virtually endless.

- Do you need it to pre-process your request by validating the user session or the form input?
- Do you need to cache or translate the request?
- Do you need to contact a third party site to enhance the request?

Your custom middleware can handle **any specific logic** your application may require.
Some useful things to implement are error handling, CORS, routing, translating.
Middleware enables lightweight, modular, and standardized frameworks, which are particularly suited for functions like APIs.

Ultimately, you can split your application into multiple middleware that process the request before it reaches your main code.
An added benefit is that the middleware can be **reused more easily** in other applications.
Even better, middleware **doesn't need to be written in the same programming language** or coding architecture.

There are two types of middleware architectures:

- One used within a framework to act as a transformative pipeline to transform a request to a response (Mezzio is in this category).
- One that acts as a filter/translation/router for data, ultimately determining if data needs to be handled, and, if so, where to funnel it.

One alternative to the middleware architecture is the MVC architecture which has grown considerably since its inception, is feature rich, modular and reusable.
For MVC, the bridge between logic and presentation is formed via routes leading to controller actions.
The primary difference to middleware is that in an MVC, a request targets a single controller which must then handle all the logic that middleware splits into flexible layers.
This architecture can lead to more laborious development down the line, when your application requires a reordering of the logic or new processing features added in the request lifecycle.

### How do you set up a middleware pipeline?

The **middleware pipeline** defines the sequence in which the middleware are executed.
Setting up the pipeline needs to be done with care to avoid unwanted behavior.
In some cases, the execution of one middleware can affect another middleware because of the way the request is passed along.
The main things to consider when setting up the pipeline are:

- Security
- Performance
- Functionality

For example, it makes sense to add an error handler early in the pipeline, even as the first entry.
If your application requires the user to be logged in, it doesn't make sense to process the request before the authentication middleware gives the go-ahead.
Also, it doesn't make sense to have an authorization middleware placed before an authentication middleware in the pipeline.

Based on the future needs of your application, the **pipeline can be easily updated** to include additional middleware.
Consider this practical example:

- Say you begin with a simple presentation site that doesn't require a lot of bells and whistles.
Your main focus is on templating and displaying mostly-static pages.
- Later on, you expand your application with a blog that allows user comments.
You suddenly require authentication, so you add it as a new layer in the middleware pipeline.
- Later still, you define different levels of access to your users via authorization.
Again, this works best as a separate middleware that must be added to the pipeline.
- An influx of bugs reported by users creates the need for additional logging, so that too can be accommodated by an update in the pipeline.
You are free to use the same middleware, in this case for logging request-response pairs for debugging, in multiple locations in your pipeline with little impact on the existing logic.

Laminas offers great solutions for scenarios just like this one via their [many high-quality components](https://docs.laminas.dev/components/).

This versatility goes a long way toward making the flow of your application easier to understand and debug.
After all, debugging and **maintaining smaller, relatively independent sections of code** is easier than working with a monolith, especially if they are **separate from the main application logic**.

By comparison, in MVC it's easier to forget to add the authentication check we mentioned before in e.g. a lesser-used action.
At the same time, MVC can potentially violate DRY principles.
Resolving these issues is second nature to middleware, which tends to use abstractions and avoids code duplication.

### When and how is middleware executed?

Like the name suggests, **middleware are executed between the request and the server response** handled by your main codebase.
The classic way of handling a request meant running through a sequence of code that is less adaptive.

And here is where middleware shines.
There are two main ways the middleware handles its input:

- It **passes control** to the next middleware in the pipeline.
- It **terminates the request** and generates a response of its own.

Normally, the execution passes through the pipeline, one middleware at a time, until the execution is passed to your main codebase which generates a response.
From than point on, the execution passes through the same middleware in reverse order, all the way to displaying the result to the user.

![middleware-graphic](/images/blog/mezzio101/mezzio101-middleware.jpg "middleware-graphic")

Based on your pipeline, the execution can end sooner and generate a relevant response, like an exception, before it reaches your main codebase.
This is a more efficient way of handling the request in certain use cases and should result in shorter response times.
The response in this case is generated by the middleware where the execution ended.

As an example, let's say that the user needs to be logged in before the system allows access.
If your pipeline has an authentication middleware, it can return an exception that redirects the user to the login page.

### Standardizing the architecture

The aim of standards is to make systems interoperable, to make code easier to read and understand, and in general to improve developers' workflow.
[The PHP Framework Interop Group](https://www.php-fig.org/) has been working diligently on defining standards for using PHP effectively.
They have also defined recommendations for higher-level architecture, like the use of `handlers` together with `middleware` which interests us for the purpose of this article.

[PSR-15](https://www.php-fig.org/psr/psr-15/) defines the mechanism for processing a [PSR-7](https://www.php-fig.org/psr/psr-7/) request and producing a response.
PSR-15 mentions interfaces that must be implemented for the handler and the middleware, as well as the recommended way to process responses and exceptions.

### Overview of middleware benefits

We already mentioned these in the sections above, but here are the benefits in an easier-to-read list:

- **Separation of Concerns**: Each middleware handles a single task like logging, authentication and error handling.
- **Reusability**: middleware are reusable in other applications.
- **Customizable**: Your custom middleware can contain any specific logic your application needs.
- **Sequential Execution**: Middleware code is executed in the sequence defined in the middleware pipeline.
- **Expandability**: The middleware pipeline can be edited to include more middleware without affecting existing processes.
- **Improved request handling**: Each middleware can either pass control to the next middleware in the pipeline or terminate the request.
- **Standardization**: This applies mostly to middleware that follow a given standard, like PSR-15, which makes the application architecture more consistent and easier to understand.

### Additional resources

[Learn more about Mezzio from the source](https://docs.mezzio.dev/)

[Laminas components](https://docs.laminas.dev/components/)

[Dotkernel Light, an implementation of Mezzio using handlers](https://github.com/dotkernel/light)

[The Slim PHP micro framework](https://www.slimframework.com/)

[The PHP Framework Interop Group's full list of PSRs](https://www.php-fig.org/psr/)

[PSR-7: The magical middleware tour](https://vimeo.com/showcase/4061778/video/177154167)

[From Helpers to Middleware](https://www.youtube.com/watch?v=v1I57-_Rsv0)
