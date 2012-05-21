PHP Coding Standard Fixer
=========================

`PHP_CodeSniffer` is a good tool to find coding standards problems in your
project but the identified problems need to be fixed by hand, and frankly,
this is quite boring on large projects! The goal of the PHP coding Standard
Fixer tool is to automate the fixing of *most* issues.

The tool knows how to fix issues for the coding standards defined in the
soon-to-be-available PSR-1 and PSR-2 documents.

Installation
------------

Download the
[`php-cs-fixer.phar`](https://github.com/fabpot/PHP-CS-Fixer/raw/master/php-cs-fixer.phar)
file and store it somewhere on your computer.

Usage
-----

The `fix` command tries to fix as much coding standards
problems as possible on a given file or directory:

    php php-cs-fixer.phar fix /path/to/dir
    php php-cs-fixer.phar fix /path/to/file

You can limit the fixers you want to use on your project by using the
`--level` option:

    php php-cs-fixer.phar fix /path/to/project --level=psr1
    php php-cs-fixer.phar fix /path/to/project --level=psr2
    php php-cs-fixer.phar fix /path/to/project --level=all

When the level option is not passed, all PSR-2 fixers and some additional ones
are run.

You can also explicitly name the fixers you want to use (a list of fixer names
separated by a comma):

    php php-cs-fixer.phar fix /path/to/dir --fixers=linefeed,short_tag,indentation

Here is the list of built-in fixers:

 * **short_tag**       [PSR-1] PHP code must use the long <?php ?> tags or the
                   short-echo <?= ?> tags; it must not use the other tag
                   variations.

 * **trailing_spaces** [PSR-2] Remove trailing whitespace at the end of lines.

 * **unused_use**      [all] Unused use statements must be removed.

 * **return**          [all] An empty line feed should precede a return
                   statement.

 * **phpdoc_params**   [all] All items of the @param phpdoc tags must be aligned
                   vertically.

 * **linefeed**        [PSR-2] All PHP files must use the Unix LF (linefeed)
                   line ending.

 * **eof_ending**      [all] A file must always ends with an empty line feed.

 * **indentation**     [PSR-2] Code must use 4 spaces for indenting, not tabs.

 * **braces**          [PSR-2] Opening braces for classes and methods must go on
                   the next line, and closing braces must go on the next
                   line after the body. Opening braces for control
                   structures must go on the same line, and closing braces
                   must go on the next line after the body.

 * **elseif**          [PSR-2] The keyword elseif should be used instead of else
                   if so that all control keywords looks like single words.

You can also use built-in configurations, for instance when ran for Symfony:

    # For the Symfony 2.1 branch
    php php-cs-fixer.phar fix /path/to/sf21 --config=symfony21

Here is the list of built-in configs:

 * **default** A default configuration

 * **sf20**    The configuration for the Symfony 2.0 branch

 * **sf21**    The configuration for the Symfony 2.1 branch

Instead of using the command line arguments, you can save your configuration
in a `.php_cs` file in the root directory of your project. It
must return an instance of `Symfony\CS\ConfigInterface` and it lets you
configure the fixers and the files and directories that need to be analyzed:

    <?php

    $finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somefile')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->fixers(array('indentation', 'elseif'))
        ->finder($finder)
    ;

Helpers
-------

If you are using Vim, install the dedicated
[plugin](https://github.com/stephpy/vim-php-cs-fixer).

Contribute
----------

The tool comes with quite a few built-in fixers and finders, but everyone is
more than welcome to contribute more of them.

### Fixers

A *fixer* is a class that tries to fix one CS issue (a `Fixer` class must
implement `FixerInterface`).

### Configs

A *config* knows about the CS level and the files and directories that must be
scanned by the tool when run in the directory of your project. It is useful
for projects that follow a well-known directory structures (like for Symfony
projects for instance).
