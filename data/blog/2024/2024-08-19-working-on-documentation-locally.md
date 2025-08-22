---
id: 2024-08-19-working-on-documentation-locally.md
author: george
title: 'Contributing to and Working on Laminas & Mezzio Documentation Locally'
draft: false
public: true
created: '2024-08-19T11:00:00+00:00'
updated: '2024-08-19T11:00:00+00:00'
tags:
  - documentation
  - contributing
---

It can be difficult to get up and running when contributing documentation changes to the Laminas and Mezzio projects.
This guide aims to show a straight-forward way of building the documentation locally so that you can preview changes in your browser before sending in a pull request.

<!--- EXTENDED -->

In most cases, submitting small fixes to spelling, and phrasing of documentation can be submitted without needing to build the documentation website at all, but for more involved work, it can be taxing to get `mkdocs` and all of its dependencies and plugins installed and working, particularly if you're used to working with PHP!

So, don't be discouraged from sending in obvious fixes - this guide does not apply there, but, for entirely new docs or significant changes, please read on…

## Requirements

You'll need to have Docker installed on your machine and be comfortable entering commands into your terminal.

## Python? Pip? Docker to the Rescue

Regardless of which OS you use, getting Python _(Required for `mkdocs`)_ installed with all the dependencies and plugins required for building the documentation is a pain. Perhaps you already write code in Python, in which case you might be using [`venv`](https://docs.python.org/3/library/venv.html) already.

Instead of installing all the requirements directly on your machine, we'll create a Docker image with all the dependencies and use that to build our docs.

## Build the Docker Image

Create an empty directory for our Docker image and cd into it. The example command below will create the directory in your home folder, but you could put this directory anywhere you like:

```bash
mkdir -p ~/mkdocs-docker && cd ~/mkdocs-docker
```

Inside this directory, create the file `Dockerfile` with the following contents:

```dockerfile
FROM python:3-alpine

# PHP and bash are required for the theme installer
RUN apk update \
    && apk upgrade --no-cache \
    && apk add --no-cache \
    php \
    bash

# Install mkdocs with required libs and plugins
RUN pip install --upgrade pip
RUN pip install \
    pyyaml \
    markdown \
    mkdocs \
    pymdown-extensions \
    markdown-callouts \
    mkdocs-redirects
```

This short `Dockerfile` will install everything we need into a small image of around 100MB.

Next, we will build this image so we can run it.

```bash
docker build -t local/mkdocs .
```

This command will build and tag the image as `local/mkdocs`. When we want to use this image, we'll use this tag to reference it. The image should be built in a handful of seconds.

## Set Up The Documentation in Your Local Project

For the next step, let's assume you're working on the `laminas/laminas-validator` documentation, and you've already cloned the source to `~/Projects/laminas-validator`.
Let's cd to that directory now:

```bash
cd ~/Projects/laminas-validator
```

The next step is to install the [Laminas documentation theme files](https://github.com/laminas/documentation-theme/).
To do this clone the repository into the root directory of the `laminas-validator` project with:

```bash
git clone git@github.com:laminas/documentation-theme.git
```

## Build the Documentation Files

The next step is to build docs with our new Docker image:

```bash
docker run -it -w /app -v ${PWD}:/app --rm local/mkdocs ./documentation-theme/build.sh -u https://github.com/laminas/laminas-validator/
```

There's a lot to unpack here, so we'll go through the command from left to right:

- `docker run` is the way that a single use container is run via Docker
- `-it` hooks up your terminal to the Docker process so that STDOUT and STDERR work as if you ran the command directly on your computer
- `-w /app` sets your current working directory to `/app` within the running container before any commands are executed
- `-v ${PWD}:/app` creates a volume on the container mapping the current working directory on your computer, in this case `~/Projects/laminas-validator` to the `/app` directory on the container.
- `--rm` will remove the container from Docker after it has exited. This just prevents `docker container ls` from listing lots of containers that have exited
- `local/mkdocs` is the image **Tag** that we specified in the build command earlier
- `./documentation-theme/build.sh -u https://github.com/laminas/laminas-validator/` runs the docs build command shipped as part of `documentation-theme`. This script invokes `mkdocs` and performs a number of additional changes to the generated HTML for the documentation website.

## Preview the Result

The generated static site will now be available, from the root of the project folder, in `./docs/html`.
Open the `./docs/html/index.html` file in your web browser to preview your documentation changes.

## Bonus Content…

### Define a Makefile

You might like to define a `Makefile` so that you don't have to remember the rather long Docker command.
As an example, put this content into the file `Makefile` at the project root:

```makefile
documentation-theme: ## fetch the documentation theme repo
	git clone git@github.com:laminas/documentation-theme.git

build-docs:
	docker run -it -w /app -v ${PWD}:/app --rm local/mkdocs ./documentation-theme/build.sh -u https://github.com/laminas/laminas-validator/
.PHONY: build-docs

docs: documentation-theme build-docs

clean:
	rm -rf documentation-theme
	rm -rf docs/html
```

Now, issuing `make docs` will fetch the theme files if they don't already exist and subsequently build the docs HTML.
You could extend this Makefile to perform other actions such as automatically opening the preview in your browser after building for example
_(On an Apple mac, a command something like `open -a Firefox ./docs/html/index.html` would suffice)_.

## Conclusion

Contributing to the documentation is _really_ helpful and valuable to us and all of our users, and hopefully, this short guide will make those contributions a little bit easier.

We look forward to your patches :)

### Links

- Helpful [advice for Makefiles](https://makefiletutorial.com/)
- `docker run` [command line arguments](https://docs.docker.com/reference/cli/docker/container/run/)
- [Laminas Documentation Theme Repository](https://github.com/laminas/documentation-theme/)
