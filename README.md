# Papi

![Papi](https://cloud.githubusercontent.com/assets/14610/9073902/16a6d906-3b05-11e5-9287-5644a96e9a82.png)

[![Build Status](https://travis-ci.org/wp-papi/papi.svg?branch=master)](https://travis-ci.org/wp-papi/papi)
[![License](https://img.shields.io/packagist/l/wp-papi/papi.svg)](https://packagist.org/packages/wp-papi/papi)

> `master` is unsafe. `2.x` is the stable branch.

Papi has a different approach on how to work with fields and page types in WordPress. The idea is coming from how Page Type Builder in EPiServer works and has been loved by the developers.

So we though why don’t use the same approach in WordPress? Papi is today running in production and has been easy to work with when it came to add new fields. Papi don’t have any admin user interface where should add all fields, we use classes in PHP, where one class represents one page type and in your class you add all fields you need. It’s that easy!

[Visit Papi’s project page](http://wp-papi.github.io/)

## Installation

Papi should be install with Composer as of 2.3.0 since it has [dependencies](https://github.com/wp-papi/papi/blob/master/composer.json).

Install it by running:

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

1. `$ vagrant up`
2. Log in to the virtual machine with `$ vagrant ssh`
3. Run `$ composer install`
4. Run `$ phpunit`
5. Done!

## Coding style

You can check if your contribution passes the styleguide by installing [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) and running the following in your project directory:

```
$ vendor/bin/phpcs -s --extensions=php --standard=phpcs.xml src/
```

## Contributing

Visit the [contributing](CONTRIBUTING.md) file.

# License

MIT © [Fredrik Forsmo](https://github.com/frozzare)
