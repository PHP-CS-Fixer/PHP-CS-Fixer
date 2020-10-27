# Contributions Are Welcome!

If you need any help, don't hesitate to ask the community on [Gitter](https://gitter.im/PHP-CS-Fixer/Lobby).

## Quick Guide

### Fixer

A *fixer* is a class that tries to fix one code style issue (a ``Fixer`` class
must implement ``FixerInterface``).

### Config

A *config* knows about the code style rules and the files and directories that
must be scanned by the tool when run in the directory of your project. It is
useful for projects that follow a well-known directory structures (like for
Symfony projects for instance).

### How-To

* [Fork](https://help.github.com/articles/fork-a-repo/) the repo.
* [Checkout](https://git-scm.com/docs/git-checkout) the branch you want to make changes on:
  * If you are fixing a bug or typo, improving tests or for any small tweak: the lowest branch where the changes can be applied. Once your Pull Request is accepted, the changes will get merged up to highest branches.
  * `master` in other cases (new feature, deprecation, or backwards compatibility breaking changes). Note that most of the time, `master` represents the next minor release of PHP CS Fixer, so Pull Requests that break backwards compatibility might be postponed.
* Install dependencies: `composer install`.
* Create a new branch, e.g. `feature-foo` or `bugfix-bar`.
* Make changes.
* If you are adding functionality or fixing a bug - add a test! Prefer adding new test cases over modifying existing ones.
* Make sure there is no wrong file permissions in the repository: `./dev-tools/check_file_permissions.sh`.
* Make sure there is no trailing spaces in the code: `./dev-tools/check_trailing_spaces.sh`.
* Update documentation: `php dev-tools/doc.php`. This requires the highest version of PHP supported by PHP CS Fixer. If it is not installed on your system, you can run it in a Docker container instead: `docker run -it --rm --user="$(id -u):$(id -g)" -w="/app" --volume="$(pwd):/app" php:7.4-cli php dev-tools/doc.php`.
* Install dev tools: `dev-tools/install.sh`
* Run static analysis using PHPStan: `php -d memory_limit=256M dev-tools/vendor/bin/phpstan analyse`
* Check if tests pass: `vendor/bin/phpunit`.
* Fix project itself: `php php-cs-fixer fix`.

## Opening a [Pull Request](https://help.github.com/articles/about-pull-requests/)

You can do some things to increase the chance that your Pull Request is accepted the first time:

* Submit one Pull Request per fix or feature.
* If your changes are not up to date, [rebase](https://git-scm.com/docs/git-rebase) your branch onto the parent branch.
* Follow the conventions used in the project.
* Remember about tests and documentation.
* Don't bump version.

## Making New Fixers

There is a [cookbook](doc/cookbook_fixers.rst) with basic instructions on how to build a new fixer. Consider reading it
before opening a PR.

## Project's Standards

* [PSR-1: Basic Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
* [PSR-2: Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR-4: Autoloading Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR-5: PHPDoc (draft)](https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md)
* [Symfony Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html)
