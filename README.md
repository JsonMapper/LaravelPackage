<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://jsonmapper.net/images/jsonmapper-light.png">
  <img alt="JsonMapper logo" src="https://jsonmapper.net/images/jsonmapper.png">
</picture>

---
**This is a Laravel package for using JsonMapper in you Laravel application.** 

JsonMapper is a PHP library that allows you to map a JSON response to your PHP objects that are either annotated using doc blocks or use typed properties.
For more information see the project website: https://jsonmapper.net

![GitHub](https://img.shields.io/github/license/JsonMapper/LaravelPackage)
![Packagist Version](https://img.shields.io/packagist/v/json-mapper/laravel-package)
![PHP from Packagist](https://img.shields.io/packagist/php-v/json-mapper/laravel-package)
![Build](https://github.com/JsonMapper/LaravelPackage/workflows/Build/badge.svg?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/JsonMapper/LaravelPackage/badge.svg?branch=master)](https://coveralls.io/github/JsonMapper/LaravelPackage?branch=master)

# Why use JsonMapper
Continuously mapping your JSON responses to your own objects becomes tedious and is error prone. Not mentioning the
tests that needs to be written for said mapping.

JsonMapper has been build with the most common usages in mind. In order to allow for those edge cases which are not 
supported by default, it can easily be extended as its core has been designed using middleware.

JsonMapper supports the following features
 * Case conversion
 * Debugging
 * DocBlock annotations
 * Final callback
 * Namespace resolving
 * PHP 7.4 Types properties
  
# Installing JsonMapper laravel package 
The installation of JsonMapper Laravel package can easily be done with [Composer](https://getcomposer.org)
```bash
$ composer require json-mapper/laravel-package
```
The example shown above assumes that `composer` is on your `$PATH`.

# Contributing
Please refer to [CONTRIBUTING.md](https://github.com/JsonMapper/LaravelPackage/blob/master/CONTRIBUTING.md) for information on how to contribute to JsonMapper Laravel package.

## List of Contributors
Thanks to everyone who has contributed to JsonMapper Laravel package! You can find a detailed list of contributors of JsonMapper on [GitHub](https://github.com/JsonMapper/LaravelPackage/graphs/contributors).

# License
The MIT License (MIT). Please see [License File](https://github.com/JsonMapper/LaravelPackage/blob/master/LICENSE) for more information.
