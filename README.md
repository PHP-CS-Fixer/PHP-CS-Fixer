<p align="center">
    <a href="https://cs.symfony.com">
        <img src="./logo.png" title="PHP CS Fixer" alt="PHP CS Fixer logo">
    </a>
</p>

PHP Coding Standards Fixer
==========================

The PHP Coding Standards Fixer (PHP CS Fixer) tool fixes your code to follow standards;
whether you want to follow PHP coding standards as defined in the PSR-1, PSR-2, etc.,
or other community driven ones like the Symfony one.
You can **also** define your (team's) style through configuration.

It can modernize your code (like converting the ``pow`` function to the ``**`` operator on PHP 5.6)
and (micro) optimize it.

If you are already using a linter to identify coding standards problems in your
code, you know that fixing them by hand is tedious, especially on large
projects. This tool does not only detect them, but also fixes them for you.

## Supported PHP Versions

* PHP 7.4
* PHP 8.0 (except PHP 8.0.0 due to [bug in PHP tokenizer](https://bugs.php.net/bug.php?id=80462))
* PHP 8.1
* PHP 8.2

> **Note**
> Each new PHP version requires a huge effort to support the new syntax.
> That's why the latest PHP version might not be supported yet. If you need it,
> please, consider supporting the project in any convenient way, for example
> with code contribution or reviewing existing PRs. To run PHP CS Fixer on yet
> unsupported versions "at your own risk" - leverage the
> [PHP_CS_FIXER_IGNORE_ENV](./doc/usage.rst#environment-options).

## Documentation

### Installation

The recommended way to install PHP CS Fixer is to use [Composer](https://getcomposer.org/download/)
in a dedicated `composer.json` file in your project, for example in the
`tools/php-cs-fixer` directory:

```console
mkdir -p tools/php-cs-fixer
composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
```

For more details and other installation methods, see
[installation instructions](./doc/installation.rst).

### Usage

Assuming you installed PHP CS Fixer as instructed above, you can run the
following command to fix the files PHP files in the `src` directory:

```console
tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
```

See [usage](./doc/usage.rst), list of [built-in rules](./doc/rules/index.rst), list of [rule sets](./doc/ruleSets/index.rst)
and [configuration file](./doc/config.rst) documentation for more details.

If you need to apply code styles that are not supported by the tool, you can
[create custom rules](./doc/custom_rules.rst).

## Editor Integration

Dedicated plugins exist for:

* [NetBeans](https://plugins.netbeans.apache.org/catalogue/?id=36)
* [PhpStorm](https://www.jetbrains.com/help/phpstorm/using-php-cs-fixer.html)
* [Sublime Text](https://github.com/benmatselby/sublime-phpcs)
* [Vim](https://github.com/stephpy/vim-php-cs-fixer)
* [VS Code](https://github.com/junstyle/vscode-php-cs-fixer)

## Community

The PHP CS Fixer is maintained on GitHub at https://github.com/PHP-CS-Fixer/PHP-CS-Fixer.
Bug reports and ideas about new features are welcome there.

You can reach us at https://gitter.im/PHP-CS-Fixer/Lobby about the project,
configuration, possible improvements, ideas and questions, please visit us!

## Contribute

The tool comes with quite a few built-in fixers, but everyone is more than
welcome to [contribute](CONTRIBUTING.md) more of them.
