PHP Coding Standard Fixer
=========================

This tool analyzes some PHP source code to fix as much coding standards
problems as possible.

Download the `php-cs-fixer.phar` file and execute it:

    php php-cs-fixer.phar fix /path/to/project

You can tweak the files and directories being analyzed by creating a `.php_cs`
file in the root directory of your project:

    <?php

    return Symfony\Component\Finder\Finder::create()
        ->name('*.php')
        ->exclude('someDir')
        ->in(__DIR__)
    ;

The `.php_cs` file must return a PHP iterator, like a Symfony Finder instance.

You can also use specialized "finders", for instance when ran for Symfony 2.0
or 2.1:

    # For the Symfony 2.0 branch
    php php-cs-fixer.phar fix /path/to/symfony/src Symfony20Finder

    # For the Symfony 2.1/master branch
    php php-cs-fixer.phar fix /path/to/symfony/src Symfony21Finder

    # For a Symfony bundle or any other project using the same Symfony CS
    php php-cs-fixer.phar fix /path/to/bundle

See http://symfony.com/doc/current/contributing/code/standards.html for more
information about the Symfony Coding Standards.
