# app.yml

`app.yml` is the main config file for your application. It's where you define your routes (and their middleware and templates), your database settings, and any plugins you're using.

Here's an example:

```yaml
# example app.yml
name: "helloworld"
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

***

- ## `name`
  The name of your application. This is available globally in templates as `app.name`.

- ## `version`
  The current version of your application. This is available globally in templates as `app.version`.

- ## `target`
  The target of your application. This will be either `development` or `production`, depending on where the application is running. Running an application with a `development` target will show warnings/exceptions instead of a default 500 page, so you should swap this to `production` when deploying. This is available globally in templates as `app.target`.

- ## `database`
  If your app uses a database, it's connection details go here. Currently MySQL (and derivatives), and SQLite are supported.

    - ### Driver (`mysql` or `sqlite`)
    The first nested key of `database` defines what DB driver your app uses.

        - ### `host` (MySQL)
        MySQL hostname

        - ### `user` (MySQL)
        Database user's username

        - ### `pass` (MySQL)
        Database user's password

        - ### `db` (MySQL)
        The database's name

        - ### `path` (SQLite)
        The location of the SQLite .db file, relative to the Conduit root directory.
        