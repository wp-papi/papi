# Papi

![Papi](https://cloud.githubusercontent.com/assets/14610/9073902/16a6d906-3b05-11e5-9287-5644a96e9a82.png)

[![Build Status](https://travis-ci.org/wp-papi/papi.svg?branch=master)](https://travis-ci.org/wp-papi/papi)
[![Coverage Status](https://img.shields.io/codecov/c/github/wp-papi/papi.svg?style=flat)](https://codecov.io/github/wp-papi/papi)
[![Latest Version](https://img.shields.io/github/release/wp-papi/papi.svg?style=flat)](https://github.com/wp-papi/papi/releases)
[![License](https://img.shields.io/packagist/l/wp-papi/papi.svg)](https://packagist.org/packages/wp-papi/papi)

> `master` is unsafe. `2.x` is the stable branch.

Papi has a different approach on how to work with fields and page types in WordPress. The idea is coming from how Page Type Builder in EPiServer works and has been loved by the developers.

So we though why don’t use the same approach in WordPress? Papi is today running in production and has been easy to work with when it came to add new fields. Papi don’t have any admin user interface where you should add all fields, we use classes in PHP, where one class represents one page type and in your class you add all fields you need. It’s that easy!

[Visit Papi’s project page](https://wp-papi.github.io/)

## Installation

If you're using Composer to manage WordPress, add Papi to your project's dependencies. Run:

```sh
composer require wp-papi/papi
```

Or manually add it to your `composer.json`:

```json
"require": {
  "php": ">=5.4.7",
  "wordpress": "~4.2",
  "wp-papi/papi": "~2.0"
}
```

## Testing

Visit the [readme](tests/README.md) file for testing.

## Coding style

You can check if your contribution passes the styleguide by installing [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) and running the following in your Papi directory:

```
$ vendor/bin/phpcs -s --extensions=php --standard=phpcs.xml src/
```

## Contributing

Visit the [contributing](CONTRIBUTING.md) file.

# License

MIT © [Fredrik Forsmo](https://github.com/frozzare)
