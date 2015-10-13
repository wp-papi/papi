# Papi Tests

## Run tests with VVV

This guide will describe how to use [VVV](https://github.com/varying-vagrant-vagrants/vvv/) to run Papi tests.

1. Download [VVV](https://github.com/varying-vagrant-vagrants/vvv/) which is an evolving Vagrant configuration focused on WordPress development.

2. Clone Papi to `path/to/vvv/www/wordpress-default/wp-content/`.

2. Go to Run  `path/to/vvv/www/wordpress-default/wp-content/papi` and run `composer install` on your host machine. If you have configured Composer right then you can run this in your VVV machine.

3. Log in to your vagrant machine and go to `/srv/www/wordpress-default/wp-content/papi`, `/srv/` is equal to your VVV directory.

4. Run `vendor/bin/phpunit` (PHPUnit is installed with Composer) and PHPUnit will start to test Papi.

## VVV tips

If using VVV you can run `xdebug_on` to turn XDebug on so you can generate code coverage.

## PHPUnit tips

You can run specific tests by providing the path and filename to the test class:

```
$ vendor/bin/phpunit tests/includes/admin
```

A text code coverage summary can be displayed using the `--coverage-text` option:

```
$ vendor/bin/phpunit --coverage-text
```

## Automated Tests

Tests are automatically run with [Travis-CI](https://travis-ci.org) for each commit and pull request. You can check the current test status [here](https://travis-ci.org/wp-papi/papi).

This is the current build status for `master` branch:

[![Build Status](https://travis-ci.org/wp-papi/papi.svg?branch=master)](https://travis-ci.org/wp-papi/papi)

## Code Coverage

Code coverage is available on [Codecov](https://codecov.io/) which receives updated data after each Travis build. You can check the current code coverage [here](https://codecov.io/github/wp-papi/papi/).
