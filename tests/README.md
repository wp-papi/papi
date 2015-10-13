# Papi Tests

## Run tests with VVV

This guide will describe how to use [VVV](https://github.com/varying-vagrant-vagrants/vvv/) to run Papi tests.

1. Download [VVV](https://github.com/varying-vagrant-vagrants/vvv/) which is an evolving Vagrant configuration focused on WordPress development.

2. Clone Papi to `path/to/vvv/www/wordpress-default/wp-content/`.

2. Go to Run  `path/to/vvv/www/wordpress-default/wp-content/papi` and run `composer install` on your host machine. If you have configured Composer right then you can run this in your VVV machine.

3. Log in to your vagrant machine and go to `/srv/www/wordpress-default/wp-content/papi`, `/srv/` is equal to your VVV directory.

4. Run `vendor/bin/phpunit` (PHPUnit is installed with Composer) and PHPUnit will start to test Papi.

## VVV tips

If using VVV you can run `xdebug_on` to turn Xdebug on so you can generate code coverage. You can turn it off by running `xdebug_off`

## PHPUnit tips

You can run specific tests by providing the path and filename to the test class:

```
$ vendor/bin/phpunit tests/includes/admin
```

You can run specific test method by using `--filter`:

```
$ vendor/bin/phpunit --filter test_save_meta_boxes
```

A text code coverage summary can be displayed using the `--coverage-text` option:

```
$ vendor/bin/phpunit --coverage-text
```

## Writing Tests

* Each test file should roughly correspond to an associated source file, e.g `src/includes/admin/class-papi-admin-ajax.php` should have a test file named `tests/cases/includes/admin/class-papi-admin-ajax-test.php`
* Each test method should cover a single method or function with one or more assertions
* A single method or function can have multiple associated test methods if it's a large or complex method
* For code that cannot be tested or should not be tested use `// @codeCoverageIgnoreStart` and `// @codeCoverageIgnoreEnd` before and after the code.
* In addition to covering each line of a method/function, make sure to test common input and edge cases. When resolving a issue you should create a test for it.
* Prefer `assertsSame()` where possible as it tests both type & equality. When testing objects you should use `assertEquals()` since object's can be different but with same data.
* Remember that only methods prefixed with `test` will be run.
* Remember that files that test code should end with `-test.php` prefix.
* Filters persist between test cases so be sure to remove them in your test method or in the `tearDown()` method.

## Automated Tests

Tests are automatically run with [Travis-CI](https://travis-ci.org) for each commit and pull request. You can check the current test status [here](https://travis-ci.org/wp-papi/papi).

This is the current build status for `master` branch:

[![Build Status](https://travis-ci.org/wp-papi/papi.svg?branch=master)](https://travis-ci.org/wp-papi/papi)

## Code Coverage

Code coverage is available on [Codecov](https://codecov.io/) which receives updated data after each Travis build. You can check the current code coverage [here](https://codecov.io/github/wp-papi/papi/).

This is the current code coverage for `master` branch:

[![Coverage Status](https://img.shields.io/codecov/c/github/wp-papi/papi.svg?style=flat)](https://codecov.io/github/wp-papi/papi)
