# Papi

[![Build Status](https://travis-ci.org/wp-papi/papi.svg?branch=master)](https://travis-ci.org/wp-papi/papi)

> `master` is unsafe. `1.x` is the stable branch.

Papi has a different approach on how to work with fields and page types in WordPress. The idea is coming from how Page Type Builder in EPiServer works and has been loved by the developers.

So we though why don’t use the same approach in WordPress? Papi is today running in production and has been easy to work with when it came to add new fields. Papi don’t have any admin user interface where should add all fields, we use classes in PHP, where one class represents one page type and in your class you add all fields you need. It’s that easy!

[Visit Papi’s project page](http://wp-papi.github.io/)

## Installation

If you're using Composer to manage WordPress, add Papi to your project's dependencies. Run:

```sh
composer require wp-papi/papi
```

Or manually add it to your `composer.json`:

```json
"require": {
  "php": ">=5.3",
  "wordpress": "3.8",
  "wp-papi/papi": "~1.2"
}
```

## Testing

1. `$ vagrant up`
2. Log in to the virtual machine with `$ vagrant ssh`
3. Run `$ composer install`
4. Run `$ phpunit`
5. Done!

## Contributing

Visit the [contributing](CONTRIBUTING.md) file.
