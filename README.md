PHP Coding Standards Fixer
==========================

The PHP Coding Standards Fixer tool fixes *most* issues in your code when you
want to follow the PHP coding standards as defined in the PSR-1 and PSR-2
documents.

If you are already using `PHP_CodeSniffer` to identify coding standards
problems in your code, you know that fixing them by hand is tedious,
especially on large projects. This tool does the job for you.

Installation
------------

### Locally

Download the
[`php-cs-fixer.phar`](http://cs.sensiolabs.org/get/php-cs-fixer.phar) file and
store it somewhere on your computer.

### Globally

You can run these commands to easily acces `php-cs-fixer` from anywhere on your system:

    $ sudo wget http://cs.sensiolabs.org/get/php-cs-fixer.phar -O /usr/local/bin/php-cs-fixer

or with curl:

    $ sudo curl http://cs.sensiolabs.org/get/php-cs-fixer.phar -o /usr/local/bin/php-cs-fixer

then:

    $ sudo chmod a+x /usr/local/bin/php-cs-fixer

Then, just run `php-cs-fixer` in order to run php-cs-fixer

Update
------

### Locally

The `self-update` command tries to update php-cs-fixer itself:

    $ php php-cs-fixer.phar self-update

### Globally

You can update php-cs-fixer through this command:

    $ sudo php-cs-fixer self-update

Usage
-----

The `fix` command tries to fix as much coding standards
problems as possible on a given file or directory:

    php php-cs-fixer.phar fix /path/to/dir
    php php-cs-fixer.phar fix /path/to/file

The `--level` option limits the fixers to apply on the
project:

    php php-cs-fixer.phar fix /path/to/project --level=psr0
    php php-cs-fixer.phar fix /path/to/project --level=psr1
    php php-cs-fixer.phar fix /path/to/project --level=psr2
    php php-cs-fixer.phar fix /path/to/project --level=all

By default, all PSR-2 fixers and some additional ones are run.

The `--fixers` option lets you choose the exact fixers to
apply (the fixer names must be separated by a comma):

    php php-cs-fixer.phar fix /path/to/dir --fixers=linefeed,short_tag,indentation

You can also blacklist the fixers you don't want if this is more convenient,
using `-name`:

    php php-cs-fixer.phar fix /path/to/dir --fixers=-short_tag,-indentation

A combination of `--dry-run`, `--verbose` and `--diff` will
display summary of proposed fixes, leaving your files unchanged.

Choose from the list of available fixers:

 * **indentation**       [PSR-2] Code must use 4 spaces for indenting, not tabs.

 * **linefeed**          [PSR-2] All PHP files must use the Unix LF (linefeed)
                     line ending.

 * **trailing_spaces**   [PSR-2] Remove trailing whitespace at the end of lines.

 * **unused_use**        [all] Unused use statements must be removed.

 * **phpdoc_params**     [all] All items of the @param phpdoc tags must be
                     aligned vertically.

 * **visibility**        [PSR-2] Visibility must be declared on all properties
                     and methods; abstract and final must be declared before
                     the visibility; static must be declared after the
                     visibility.

 * **return**            [all] An empty line feed should precede a return
                     statement.

 * **short_tag**         [PSR-1] PHP code must use the long <?php ?> tags or the
                     short-echo <?= ?> tags; it must not use the other tag
                     variations.

 * **braces**            [PSR-2] Opening braces for classes, interfaces, traits
                     and methods must go on the next line, and closing
                     braces must go on the next line after the body. Opening
                     braces for control structures must go on the same line,
                     and closing braces must go on the next line after the
                     body.

 * **include**           [all] Include and file path should be divided with a
                     single space. File path should not be placed under
                     brackets.

 * **php_closing_tag**   [PSR-2] The closing ?> tag MUST be omitted from files
                     containing only PHP.

 * **extra_empty_lines** [all] Removes extra empty lines.

 * **psr0**              [PSR-0] Classes must be in a path that matches their
                     namespace, be at least one namespace deep, and the
                     class name should match the file name.

 * **controls_spaces**   [all] A single space should be between: the closing
                     brace and the control, the control and the opening
                     parenthese, the closing parenthese and the opening
                     brace.

 * **elseif**            [PSR-2] The keyword elseif should be used instead of
                     else if so that all control keywords looks like single
                     words.

 * **eof_ending**        [PSR-2] A file must always end with an empty line feed.


The `--config` option customizes the files to analyse, based
on some well-known directory structures:

    # For the Symfony 2.1 branch
    php php-cs-fixer.phar fix /path/to/sf21 --config=sf21

Choose from the list of available configurations:

 * **default** A default configuration

 * **magento** The configuration for a Magento application

 * **sf20**    The configuration for the Symfony 2.0 branch

 * **sf21**    The configuration for the Symfony 2.1 branch

The `--dry-run` option displays the files that need to be
fixed but without actually modifying them:

    php php-cs-fixer.phar fix /path/to/code --dry-run

Instead of using command line options to customize the fixer, you can save the
configuration in a `.php_cs` file in the root directory of
your project. The file must return an instance of
`Symfony\CS\ConfigInterface`, which lets you configure the fixers, the files,
and directories that need to be analyzed:

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

Dedicated plugins exist for:

* [Vim](https://github.com/stephpy/vim-php-cs-fixer)
* [Sublime Text 2](https://github.com/benmatselby/sublime-phpcs)

Contribute
----------

The tool comes with quite a few built-in fixers and finders, but everyone is
more than welcome to [contribute](https://github.com/fabpot/php-cs-fixer) more
of them.

### Fixers

A *fixer* is a class that tries to fix one CS issue (a `Fixer` class must
implement `FixerInterface`).

### Configs

A *config* knows about the CS level and the files and directories that must be
scanned by the tool when run in the directory of your project. It is useful
for projects that follow a well-known directory structures (like for Symfony
projects for instance).
