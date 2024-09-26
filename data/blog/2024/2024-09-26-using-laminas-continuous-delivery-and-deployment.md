---
id: 2024-09-26-using-laminas-continuous-delivery-and-deployment
author: julian
title: 'Using Laminas Continuous Delivery and Deployment'
draft: false
public: true
created: '2024-09-26T11:00:00-01:00'
updated: '2024-09-26T11:00:00-01:00'
tags:
  - continuous integration
---

We detailed the meaning and some inner workings of Continuous Integration (**CI**) in a [previous article](https://getlaminas.org/blog/2024-08-05-using-laminas-continuous-integration.md.html), so here we will focus on what happens after CI completes successfully.

CI is followed by **Continuous Delivery** (**CD**) which gets the code **ready for deployment to the production or staging environment**.
This ensures that the code is ready to be released **manually**.
**Continuous Deployment** goes one step further and **automates the release** itself.
The development team has to decide on what CD is right for them, but in both cases the end result is **more frequent and reliable software updates**.

<!--- EXTENDED -->

### Continuous Delivery vs. Continuous Deployment

Continuous Delivery ensures that the updates you have been working hard on are ready to be deployed to the production or staging environment.
This means that your code has already passed a battery of unit, integration and system tests.
GitHub Actions can automate this process by having you define the steps that follow right before you get to the release itself, which is still performed manually.
Continuous Deployment adds the final step by performing the release automatically.

### How does Laminas Continuous Deployment work?

Let's investigate a **practical example** for creating a release:

- **Push your updates** onto a separate branch, to avoid conflicts with other developers working on the same code.
- Optionally, **merge the update** into a 'developer' branch, which can result in conflicts.
- Merge the 'developer' branch into the 'main' branch. Note the branch setup and names may differ.
- Optionally, **update your CHANGELOG.md** file.
- **Create a new release tag** which can follow the {MAJOR}.{MINOR}.{BUGFIX} versioning format.
- **Push the release.**
- **Create the release** on GitHub using the new tag and the description from the CHANGELOG.md file.

**Laminas creates a milestone** for each new release that may include several issues and pull requests.
When the milestone is closed, **CD automates** the whole process above by triggering the **workflow** below.

- Pull the milestone description.
- Pull the list of issues and pull requests, along with their authors, to include in the *CHANGELOG.md* file.
- Add the text generated in the previous step to the CHANGELOG.md file
- Create a tag using the signing key and git author/email, using the same description as in the changelog entry.
- Create a release on GitHub.
- Check if there is already a newer tag and create a merge request with that branch. Alternatively, if no newer release branch exists, create a new minor release.
- Switch the default branch to the next release branch.
- Update the CHANGELOG.md file.
- Create milestones for the next patch, minor, and major releases, if they do not already exist.

### Final words

The examples presented in this article are meant as a guide.
You can adapt the CD workflow based on your project's needs.
The great thing about the Laminas CD solution is that it's independent of programming language, so it can be used for any repository.
Very much like with CI, the Laminas CD implementation encourages consistency.
Maintainers are able to add contributions easier and post more reliable releases.
New contributors have a smoother onboarding, given the well-defined steps that help both the open source scene and private projects.