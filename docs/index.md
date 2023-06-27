# Introduction

## What *is* Conduit?

**Conduit** is a simple, opinionated OOP microframework for developing applications in PHP. It handles database abstraction for MySQL (and it's derivatives), frontend templating with Twig, and YAML-based page routing.

Bringing together a collection of pre-configured starting packages with strong folder conventions, Conduit lets you start developing apps without having to worry about boilerplate - all the way from static sites to full applications.

## Installation

### Requirements

- [Composer](https://getcomposer.org)
- Apache2, NGINX, or any other server that features URL rewriting.
- PHP 8.1 or newer
- `PDO` extension for Database support (optional)

The easiest (and recommended) way to get started for local development with Conduit, is using [**DDEV**](https://ddev.com/).

### Creating a new conduit-project

!!! note "Heads up!"
    You'll need a Docker provider installed on your system before installing and using DDEV.

    Read more about Docker/DDEV installation [over at the DDEV docs](https://ddev.readthedocs.io/en/stable/users/install/docker-installation/).

Once you've ensured `composer` is installed globally, navigate to where you keep your web projects.
This doesn't need to be anywhere specific, but a commonly used location is `~/Projects`

```shell
cd ~
mkdir Projects
cd Projects
```

Next, run `create-project` to create a new local Conduit project in the `HelloWorld` directory:
```shell
composer create-project loft39/conduit-project HelloWorld
```

This will clone the repo and run `composer install` automatically. It'll then run the `ddev config` wizard to set up your local environment.

Both the web root (web/) and application type **must** be kept as the defaults, so unless you want to specify a different project name, press enter until you see `Configuration complete.`, then move to the project directory and run `ddev start` to start the app.

```shell
cd HelloWorld
ddev start
```