<p align="center">
    <a href="https://cs.symfony.com">
        <img src="./logo.png" title="PHP CS Fixer" alt="PHP CS Fixer logo">
    </a>
</p>

# PHP Coding Standards Fixer

The PHP Coding Standards Fixer (PHP CS Fixer) fixes your code to follow the standards.

If you are already using a linter to identify coding standards problems in your
code, you know that fixing them by hand is tedious, especially on large
projects. This tool not only detects them, but also fixes them for you.

PHP CS Fixer has built-in rule sets, whether you want to follow PHP coding standards as defined by [PHP-FIG's PER Coding Style](https://www.php-fig.org/per/coding-style/),
a wide community like the [Symfony](https://symfony.com/doc/current/contributing/code/standards.html),
or [our opinionated one](./doc/ruleSets/PhpCsFixer.rst).
You can **also** define your (team's) style through the [configuration file](./doc/config.rst).

PHP CS Fixer can not only unify the style of your code, but also help to modernize your codebase with
PHP rule sets like [@PHP85Migration](./doc/ruleSets/PHP85Migration.rst) - helping you to migrate to
modern syntax, or simply move away from deprecated one - like [no_unset_cast](./doc/rules/cast_notation/no_unset_cast.rst), as well as to
upgrade your PHPUnit tests with sets like [@PHPUnit91Migration:risky](./doc/ruleSets/PHPUnit91MigrationRisky.rst).

## Supported PHP Versions

* PHP 7.4 - PHP 8.4

> [!NOTE]
> Each new PHP version requires a huge effort to support the new syntax.
> That's why the latest PHP version might not be supported yet. If you need it,
> please consider supporting the project in any convenient way, for example,
> with code contributions or reviewing existing PRs. To run PHP CS Fixer on yet
> unsupported versions "at your own risk" - use `--allow-unsupported-php-version=yes` option.

## Documentation

### Installation

The recommended way to install PHP CS Fixer is to use [Composer](https://getcomposer.org/download/):

```sh
composer require --dev friendsofphp/php-cs-fixer
## or when facing conflicts in dependencies:
composer require --dev php-cs-fixer/shim
```

For more details and other installation methods (also with Docker or behind CI), see
[installation instructions](./doc/installation.rst).

### Usage

Assuming you installed PHP CS Fixer as instructed above, you can run the
following command to fix the PHP files in the `src` directory:

```sh
./vendor/bin/php-cs-fixer fix src
```

See [usage](./doc/usage.rst), list of [built-in rules](./doc/rules/index.rst), list of [rule sets](./doc/ruleSets/index.rst)
and [configuration file](./doc/config.rst) documentation for more details.

If you need to apply code styles that are not supported by the tool, you can
[create custom rules](./doc/custom_rules.rst).

## Editor Integration

Native support exists for:

* [PhpStorm](https://www.jetbrains.com/help/phpstorm/using-php-cs-fixer.html)

Community plugins exist for:

* [NetBeans](https://plugins.netbeans.apache.org/catalogue/?id=36)
* [Sublime Text](https://github.com/benmatselby/sublime-phpcs)
* [Vim](https://github.com/stephpy/vim-php-cs-fixer)
* [VS Code](https://github.com/junstyle/vscode-php-cs-fixer)

## Community

The PHP CS Fixer is maintained on GitHub at <https://github.com/PHP-CS-Fixer/PHP-CS-Fixer>.
Contributions, bug reports and ideas about new features are welcome there.

You can reach us in the [GitHub Discussions](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/discussions/) regarding the
project, configuration, possible improvements, ideas and questions.

## Contribute

The tool comes with quite a few built-in fixers, but everyone is more than
welcome to [contribute](./CONTRIBUTING.md) more of them.
