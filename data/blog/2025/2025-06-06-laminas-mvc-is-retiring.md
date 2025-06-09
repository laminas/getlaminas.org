---
id: 2025-06-06-laminas-mvc-is-retiring
author: bidi
title: 'Laminas MVC Is Retiring'
draft: false
public: true
created: '2025-06-06T11:00:00-01:00'
updated: '2025-06-06T11:00:00-01:00'
tags:
  - MVC
  - middleware
  - framework
  - software architecture
  - software lifecycle
openGraphImage: '2025-06-06-laminas-mvc-is-retiring.png'
openGraphDescription: 'Laminas MVC Is Retiring'
---

The Laminas MVC (Model-View-Controller) framework has proven itself over the years as a viable solution for enterprise applications.
Given its versatility, it was seen as a good starting point by many professional PHP developers.
Still, a number of reasons have determined the members of the Laminas Technical Steering Committee to discontinue Laminas MVC.

<!--- EXTENDED -->

In the first stage, which lasts until PHP 8.5 is released, MVC will be marked as `security-only` and after that it will be `archived`.
In this article we will explore the reasoning behind the decision taken by the Laminas TCS members.

### Why discontinue Laminas MVC?

The **main reason** why MVC is set to be discontinued is that there is simply **no time and resources** to maintain it.
This is reinforced by the **little activity on Laminas MVC** in the past few months.
Many TSC members are seeing Laminas MVC as a legacy code that is more difficult to work with.
Some have expressed a lack of desire to return to Laminas MVC after using **Mezzio** middleware.

TSC members have shifted their focus to [Mezzio](https://github.com/mezzio/mezzio) which is a good alternative, a worthy successor to MVC and the **second reason** for their decision regarding MVC.
Having the **Mezzio alternative** ready to use makes letting go of MVC a lot easier.
Mezzio and its dependencies are already a handful to maintain, but this framework has become the de facto solution for new projects of late.

### The steps leading to the discontinuation on MVC

A major decision like discontinuing a central product cannot be taken lightly.
The TSC members agree that Laminas MVC **cannot be archived** just yet, because it is **still in use** by many active applications.
Instead it's going to be put in `security-only` mode until PHP 8.5 is released, to allow developers of legacy applications to update their code by the time MVC is fully abandoned and archived.
The shift of focus toward **middleware**, PSR-7, PSR-15 and PSR-17 will be **actively publicized** as a collective effort by TSC members moving forward.

### The middleware alternative

As we have already mentioned, **Mezzio** is the recommended alternative to replace MVC.

- Mezzio implements several **PSRs** defined by the PHP Framework Interop Group to promote code consistency that leads to better code readability and maintainability:
    - [PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/).
    - [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/).
- Mezzio uses **ServiceManager 4** which implements [PSR-11 Container version 2](https://github.com/php-fig/container).
- The long list of **Laminas packages** that can be easily integrated into your project also implement PSRs.
- The **middleware architecture** has some key characteristics:
    - It is minimalist, flexible and more decoupled.
    - It uses dependency injection which improves maintainability and development speed.

Basically, anything you can do with Laminas MVC can be done better with Mezzio.

### How to migrate from Laminas MVC to Mezzio

There is **no simple path** or step-by-step guide to migrate from Laminas MVC to Mezzio.
The best course of action is to **consult a TSC member** for advice on moving forward.
Alternatively, you can contact a commercial vendor to analyze your application and figure out a customized solution.

### Additional resources

[Mezzio](https://github.com/mezzio/mezzio)

[What Is Mezzio? Why Would I Use It?](https://www.zend.com/resources/what-mezzio-why-would-i-use-it)

[Laminas ServiceManager 4](https://github.com/laminas/laminas-servicemanager)

[PSR-11: Container interface](https://www.php-fig.org/psr/psr-11/)

[PSR-11: Container interface - version 2 on GitHub](https://github.com/php-fig/container)

[PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/)

[PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/)

[TSC Minutes 2025-05-05](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2025-05-05-TSC-Minutes.md)
