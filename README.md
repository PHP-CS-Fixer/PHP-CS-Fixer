PHP Coding Standard Fixer
=========================

`PHP_CodeSniffer` is a good tool to find coding standards problems in your
project but the identified problems need to be fixed by hand, and frankly,
this is quite boring on large projects! The goal of the PHP coding Standard
Fixer tool is to automate the fixing of *most* issues.

The tool knows how to fix issues for the coding standards defined in the
soon-to-be-available PSR-1 and PSR-2 documents.

Usage
-----

Download the `php-cs-fixer.phar` file and execute it for a given directory:

    php php-cs-fixer.phar fix /path/to/project

You can limit the fixers you want to use on your project by using the
`--level` option:

    php php-cs-fixer.phar fix /path/to/project --level=psr1
    php php-cs-fixer.phar fix /path/to/project --level=psr2
    php php-cs-fixer.phar fix /path/to/project --level=all

When the level option is not passed, all PSR2 fixers and some additional ones
are run.

You can tweak the files and directories being analyzed by creating a `.php_cs`
file in the root directory of your project:

    <?php

    return Symfony\Component\Finder\Finder::create()
        ->name('*.php')
        ->exclude('someDir')
        ->in(__DIR__)
    ;

The `.php_cs` file must return a PHP iterator (that returns SplFileInfo
instances), like a Symfony
[Finder](http://symfony.com/doc/current/components/finder.html) instance for
example.

You can also use specialized "finders", for instance when ran for Symfony 2.0
or 2.1:

    # For the Symfony 2.0 branch
    php php-cs-fixer.phar fix /path/to/symfony/src Symfony20Finder

    # For the Symfony 2.1/master branch
    php php-cs-fixer.phar fix /path/to/symfony/src Symfony21Finder

    # For a Symfony bundle or any other project using the same Symfony CS
    php php-cs-fixer.phar fix /path/to/bundle

If you are using Vim, install the dedicated
[plugin](https://github.com/stephpy/vim-php-cs-fixer).

Contribute
----------

The tool comes with quite a few built-in fixers and finders, but everyone is
more than welcome to contribute more of them.

### Fixers

A *fixer* is a class that tries to fix one CS issue (a `Fixer` class must
implements `FixerInterface`).

### Finders

A *finder* filters the files and directories scanned by the tool when run in
the directory of your project when the project follows a well-known directory
structures (like for Symfony projects for instance).
