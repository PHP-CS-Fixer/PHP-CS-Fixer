# Contributions Are Welcome!

If you need any help, don't hesitate to ask the community using [GitHub Discussions](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/discussions/categories/q-a).

## Glossary

### Fixer

A *fixer* is a class that tries to fix a single code style issue (a ``Fixer`` class must implement ``FixerInterface``).

### Ruleset

A *ruleset* is a collection of rules (*fixers*) that may be referenced in the config file similarly to a single *fixer*. When you work on existing fixer please keep in mind it can be a part of a *ruleset*(s) and changes may affect many users. When working on new *fixer* please consider if it should be added to some *ruleset*(s).

### Config

A *config* knows about the code style rules and the files and directories that must be scanned by the tool when run in the context of your project. It is useful for projects that follow a well-known directory structures, but the tool is not limited to any specific structure, and you can configure it in a very flexible way.

## How to contribute

ℹ️ **IMPORTANT**: before contributing with really significant changes that require a lot of effort or are crucial from this tool's architecture perspective, please open [RFC on GitHub Discussion](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/discussions/categories/rfc). The development effort should start only after the proposal is discussed and the approach aligned.

### Development

* [Fork](https://help.github.com/articles/fork-a-repo/) this repository. You can use native Git approach or use [`gh` CLI tool](https://cli.github.com/).
* Create new branch on top of the latest revision of `master` branch (if you already had project locally, then make sure to update this branch before going to next steps). It's good when branch's name reflects intent of the changes, but this is not strict requirement since pull request provides description of the change. However, with good branch naming it's easier to work on multiple changes simultaneously.
* Install dependencies by running `composer update` (since project does not contain `composer.lock` it's better to ensure latest versions of packages by running `update` command instead of `install`).
* Make changes. Please remember that **all** changes have to be covered by tests.
  * if you work on a bug fix, please start with reproducing the problem by adding failing test case(s). When you have failing test case(s), you can [create pull request](#opening-a-pull-request) just to reproduce fail in the CI. Then you can provide fix _in the subsequent commits_, it will make code review easier. It's allowed to modify existing test cases in bug fix pull request, but *only if* current behavior is proved to be invalid.
  * if you work on existing fixers then don't change existing test cases, because these are contract between the maintainers and users (they ensure how tool works). Add new test cases that cover provided changes - preferred way of defining test cases is with [data provider](https://docs.phpunit.de/en/10.0/writing-tests-for-phpunit.html#data-providers) which uses `yield` with proper case description as a key (e.g. `yield 'Some specific scenario' => ['some', 'example', 'data'];`). Codebase may still contain test cases in different format, and it's totally acceptable to use `yield` approach next to existing `return` usages.
* Update documentation: `composer docs`. This requires the highest version of PHP supported by PHP CS Fixer. If it is not installed on your system, you can run it in a Docker container instead: `docker compose run php-8.2 php dev-tools/doc.php`.
* Run QA suite: `composer qa`.
* Fix project itself (if needed): `composer cs:fix`.

### Opening a [pull request](https://help.github.com/articles/about-pull-requests/)

You can do some things to increase the chance that your pull request is accepted without communication ping-pong between you and the reviewers:

* Submit [single](https://en.wikipedia.org/wiki/Single-responsibility_principle) pull request per fix or feature.
* Don't amend commits because it makes review rounds harder - all commits from your branch will be squashed (without commit messages) during merge, so you can treat pull request as a playground, without keeping everything tidy at any point.
* If your changes are not up-to-date, [rebase](https://git-scm.com/docs/git-rebase) your branch onto the parent branch. Do it regularly whenever your branch is behind `master` branch, that will eliminate risk of problems after the merge.
* Follow the conventions used in the project.
* Remember about tests and documentation.
* Don't bump `PhpCsFixer\Console\Application::VERSION`, it's done during release.

## Working With Docker

This project provides a Docker setup that allows working on it using any of the PHP versions supported by the tool.

To use it, you first need to install [Docker](https://docs.docker.com/get-docker/) ([Docker Compose](https://docs.docker.com/compose/) is a built-in plugin of the main tool).

Next, copy [`compose.override.dist.yaml`](./compose.override.dist.yaml) to `compose.override.yaml` and edit it to your needs. The relevant parameters that might require some tweaking have comments to help you.

You can then build the images:

```console
docker compose build --parallel
```

Now you can run commands needed to work on the project. For example, say you want to run PHPUnit tests on PHP 8.2:

```console
docker compose run php-8.2 vendor/bin/phpunit
```

Sometimes it can be more convenient to have a shell inside the container:

```console
docker compose run php-7.4 sh
/app vendor/bin/phpunit
```

The images come with an [`xdebug` script](github.com/julienfalque/xdebug/) that allows running any PHP command with Xdebug enabled to help debug problems.

```console
docker compose run php-8.2 xdebug vendor/bin/phpunit
```

If you're using PhpStorm, you need to create a [server](https://www.jetbrains.com/help/phpstorm/servers.html) with a name that matches the `PHP_IDE_CONFIG` environment variable defined in the Docker Compose configuration files, which is `php-cs-fixer` by default.

All images use port 9003 for debug connections.

## Making New Fixers

There is a [cookbook](doc/cookbook_fixers.rst) with basic instructions on how to build a new fixer. Consider reading it before opening a PR.

## Project's Standards

* [PSR-1: Basic Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
* [PSR-2: Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR-4: Autoloading Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR-5: PHPDoc (draft)](https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md)
* [Symfony Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html)
