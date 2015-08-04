# Papi

[![Build Status](https://travis-ci.org/wp-papi/papi.svg?branch=master)](https://travis-ci.org/wp-papi/papi) [![Join the chat at https://gitter.im/wp-papi/papi](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/wp-papi/papi?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

> `master` is unsafe. `2.x` is the stable branch.

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

Papi has a `phpcs.rulset.xml` so you can check the source code coding style.

```
$ gulp phpcs

// or

$ vendor/bin/phpcs -s --extensions=php --standard=phpcs.ruleset.xml src/
```

## Contributing

Visit the [contributing](CONTRIBUTING.md) file.

# License

MIT © [Fredrik Forsmo](https://github.com/frozzare)
