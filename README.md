# Papi

![Papi](https://cloud.githubusercontent.com/assets/14610/9073902/16a6d906-3b05-11e5-9287-5644a96e9a82.png)

[![Latest Version](https://img.shields.io/github/release/wp-papi/papi.svg?style=flat)](https://github.com/wp-papi/papi/releases)
[![License](https://img.shields.io/packagist/l/wp-papi/papi.svg)](https://packagist.org/packages/wp-papi/papi)
[![Gitter](https://badges.gitter.im/wp-papi/papi.svg)](https://gitter.im/wp-papi/papi?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)
[![No Maintenance Intended](http://unmaintained.tech/badge.svg)](http://unmaintained.tech/)

> `master` is unsafe. `3.x` is the stable branch.

Papi has a different approach on how to work with fields and page types in WordPress. The idea is coming from how Page Type Builder in EPiServer works and has been loved by the developers.

So we though why don’t use the same approach in WordPress? Papi is today running in production and has been easy to work with when it came to add new fields. Papi don’t have any admin user interface where you should add all fields, we use classes in PHP, where one class represents one page type and in your class you add all fields you need. It’s that easy!

[Visit Papi’s project page](https://wp-papi.github.io/)

## A message from the author

v3.2.0 will likely be the last release I plan to work on as the core maintainer of Papi since my focus has shifted from WordPress to doing more JavaScript and Go projects. I hope you understand my decision to step back from the project, if you have any questions or would be interested in take over some of the maintenance of the project please let me know. I will still be around answering questions and helping any new maintainers. Some bug fixes and/or pull request may be added (but no new versions) since me and my colleagues use Papi internally and will be continuing doing it.

## Installation

If you're using Composer to manage WordPress, add Papi to your project's dependencies. Run:

```sh
composer require wp-papi/papi
```

Or manually add it to your `composer.json`:

```json
"require": {
  "php": "^^7.4",
  "wordpress": "^4.6",
  "wp-papi/papi": "^3.2"
}
```

## Build CSS and JavaScript

Install dependencies:

```
make deps
```

Build CSS:

```
make css
```

Build JavaScript:

```
make js
```

## Testing

Visit the [readme](tests/README.md) file for testing.

## Coding style

You can check if your contribution passes the styleguide by installing [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) and running the following in your Papi directory:

```
make lint:php
```

## Contributing

Visit the [contributing](CONTRIBUTING.md) file.

## Security

If you discover a security vulnerability within this package, please send an e-mail to Fredrik Forsmo at security@frozzare.com. All security vulnerabilities will be promptly addressed.

## License

MIT © [Fredrik Forsmo](https://github.com/frozzare)
