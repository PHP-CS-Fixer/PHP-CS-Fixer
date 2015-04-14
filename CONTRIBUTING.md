# Contributions are welcome!

## Quick guide

 * Fork the repo.
 * Checkout the branch you want to make changes on:
  * Master if you make changes to the code that are not backward compatible.
  * Default branch when adding new features.
  * Branch before the default if you are fixing a bug for an existing feature (or the default/master branch if the feature was introduced in that version).
 * Install dependencies: `composer install`.
 * Create branch, e.g. `feature-foo` or `bugfix-bar`.
 * Make changes.
 * If you are adding functionality or fixing a bug - add a test!
 * Fix project itself: `php php-cs-fixer fix`.
 * Regenerate readme: `php php-cs-fixer readme > README.rst`. Do not modify `README.rst` manually!
 * Check if tests pass: `phpunit` [(4.*)](https://phpunit.de/manual/current/en/installation.html)

## Opening a pull request

You can do some things to increase the chance that your pull request is accepted the first time:

 * Submit one pull request per fix or feature.
 * If your changes are not up to date - rebase your branch on master.
 * Follow the conventions used in the project.
 * Remember about tests and documentation.
 * Don't bump version.

## Making new fixers

There is a [cookbook](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/COOKBOOK-FIXERS.md) with basic instructions on how to build a new fixer. Consider reading it
before opening a PR.

## Project's standards

 * [PSR-0: Autoloading Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
 * [PSR-1: Basic Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
 * [PSR-2: Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
 * [Symfony Coding Standards](http://symfony.com/doc/current/contributing/code/standards.html)
 * [Symfony Documentation Standards](http://symfony.com/doc/current/contributing/documentation/standards.html)
 * Keep the order of class elements: static properties, instance properties, constructor (or setUp for PHPUnit), destructor (or tearDown for PHPUnit), static methods, instance methods, magic static methods, magic instance methods.
