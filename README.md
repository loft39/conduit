# Conduit

**Conduit** is a simple, opinionated microframework for PHP CRUD applications, that takes care of routing, and
simple database access.

![Version](https://img.shields.io/github/v/tag/charliewilson/conduit?label=version) [![License](http://img.shields.io/:license-mit-blue.svg)](http://badges.mit-license.org)

---

## Installation

### Requirements

- Composer
- Apache2, NGINX, or any other server that features URL rewriting.
- PHP 8.1 or newer
- `PDO` extension for Database support (optional)

The easiest (and recommended) way to get started for local development with Conduit, is using
[**DDEV**](https://ddev.com/). Read more about Docker/DDEV installation
[over at the DDEV docs](https://ddev.readthedocs.io/en/stable/users/install/docker-installation/).

### Installing with Composer

Run `create-project` to create a new local Conduit project in the `HelloWorld` directory:
```shell
composer create-project charliewilson/conduit HelloWorld
```
If you want to run the latest test version, specify the `dev-master` version:

```shell
composer create-project charliewilson/conduit HelloWorld dev-master
```

This will clone the repo and run `composer install` automatically. It'll then run the `ddev config` wizard to set up
your local environment. The defaults are fine (unless you want to specify a different project name), so just press
enter until you see `Configuration complete.`, then move to the project directory and run `ddev start` to start the app.

```shell
cd HelloWorld
ddev start
```

### Updating Conduit

Simply download the latest release and overwrite the existing `conduit` folder in your project root with the new one.
Easy!

---

## Configuration

Conduit apps' main configuration lives in `/app/app.yml`:

```yaml
# Conduit app configuration
name: HelloWorld
version: "1.0"
target: development
# Database - uncomment the below if you're using MySQL and DDEV.
#database:
#  mysql:
#    host: db
#    user: db
#    pass: db
#    db: db
routes:
  "/":
    GET:
      middleware: Home
      template: index.twig
```