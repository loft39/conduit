# Installation

## Requirements

- [Composer](https://getcomposer.org)
- Apache2, NGINX, or any other server that features URL rewriting.
- PHP 8.1 or newer
- PHP `PDO` extension for Database support (optional)

The easiest (and recommended) way to get started for local development with Conduit, is using [**DDEV**](https://ddev.com/).

## Creating a new conduit-project

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

Both the web root (web/) and application type (php) **must** be kept as the defaults, so unless you want to specify a different project name, press enter until you see `Configuration complete.`, then move to the project directory and run `ddev start` to start the app.

```shell
cd HelloWorld
ddev start
```

You'll then see the "Hello, Conduit" screen, which means your new project has been created successfully!

If you're developing an application or site that needs a MySQL or SQLite database, set that up next, or if you just need
routing/templates (and optional middleware), skip ahead to *Routing and Templates*