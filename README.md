Symfony Demo Application
========================

The "Symfony Demo Application" is a reference application created to show how
to develop Symfony applications following the recommended best practices.

Requirements
------------

  * PHP 7.1.3 or higher;
  * PDO-SQLite PHP extension enabled;
  * and the [usual Symfony application requirements][1].

Installation
------------

Execute this command to install the project:

```bash
$ composer create-project symfony/symfony-demo
```

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

Usage
-----

There's no need to configure anything to run the application. Just execute this
command to run the built-in web server and access the application in your
browser at <http://localhost:8000>:

```bash
$ cd symfony-demo/
$ make up
```

To see all commands available run:

```bash
$ cd symfony-demo/
$ make
```

Alternatively, you can [configure a fully-featured web server][2] like Nginx
or Apache to run the application.

Tests
-----

Execute this command to run tests:

```bash
$ cd symfony-demo/
$ make test
```

Or this command to run tests and get the coverage:

```bash
$ cd symfony-demo/
$ make test_cov
```

Integration with PHPStorm
-------------------------

Integration with PHPStorm is straight forward.

Configure the servers so we can debug a request made from the browser:
![PHPStorm servers config](docs/IDE/PHPStorm/IDE_PHPSTORM_servers.png)

Configure the CLI so we can run the tests:
![PHPStorm CLI config](docs/IDE/PHPStorm/IDE_PHPSTORM_cli_interpreter.png)

Configure the test run itself:
![PHPStorm tests config](docs/IDE/PHPStorm/IDE_PHPSTORM_tests_run.png)

[1]: https://symfony.com/doc/current/reference/requirements.html
[2]: https://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html
