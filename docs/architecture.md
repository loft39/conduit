# Architecture

## Introduction

Conduit has a very rigid directory structure, which aims to make development easier by assuming the following about your
application;



XXX

### Directory Structure

#### /app

This is where all the code and files specific to your application live.

##### app.yml

`app.yml` is the main config file for your application. It's where you define your routes (and their middleware and templates), your database settings, and any plugins you're using.

Here's an example:

```yaml
# example app.yml
name: helloworld
version: "1.0"
target: development
database:
  mysql:
    host: db
    user: db
    pass: db
    db: db
routes:
  "/":
    GET:
      template: index.twig
      middleware: Home
plugins:
  "user":
    foo: bar
```

- `app` - This is where all your app-specific code goes:
    - `/database` - If you're using the `SQLite` driver, this is where you store your `app.db`.
    - `/middleware` - Middleware for your `app.yml` routes live here.
    - `/objects` - If you're using a database, classes in this folder map to an `obj_***` table in your DB.
    - `/templates` - Twig templates live in this folder.
    - `app.yml` - The main config file of your 