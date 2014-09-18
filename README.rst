PHP Coding Standards Fixer
==========================

The PHP Coding Standards Fixer tool fixes *most* issues in your code when you
want to follow the PHP coding standards as defined in the PSR-1 and PSR-2
documents.

If you are already using ``PHP_CodeSniffer`` to identify coding standards
problems in your code, you know that fixing them by hand is tedious, especially
on large projects. This tool does the job for you.

Requirements
------------

PHP needs to be a minimum version of PHP 5.3.6

Installation
------------

Locally
~~~~~~~

Download the `php-cs-fixer.phar`_ file and store it somewhere on your computer.

Globally (manual)
~~~~~~~~~~~~~~~~~

You can run these commands to easily access ``php-cs-fixer`` from anywhere on
your system:

.. code-block:: bash

    $ sudo wget http://get.sensiolabs.org/php-cs-fixer.phar -O /usr/local/bin/php-cs-fixer

or with curl:

.. code-block:: bash

    $ sudo curl http://get.sensiolabs.org/php-cs-fixer.phar -o /usr/local/bin/php-cs-fixer

then:

.. code-block:: bash

    $ sudo chmod a+x /usr/local/bin/php-cs-fixer

Then, just run ``php-cs-fixer``.

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

To install PHP-CS-Fixer, install Composer and issue the following command:

.. code-block:: bash

    $ ./composer.phar global require fabpot/php-cs-fixer @stable

Then, make sure you have ``~/.composer/vendor/bin`` in your ``PATH``, and
you're good to go:

.. code-block:: bash

    export PATH="$PATH:$HOME/.composer/vendor/bin"

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

PHP-CS-Fixer is part of the homebrew-php project. Follow the installation
instructions at https://github.com/josegonzalez/homebrew-php if you don't
already have it.

.. code-block:: bash

    $ brew tap josegonzalez/homebrew-php
    $ brew install php-cs-fixer

Update
------

Locally
~~~~~~~

The ``self-update`` command tries to update ``php-cs-fixer`` itself:

.. code-block:: bash

    $ php php-cs-fixer.phar self-update

Globally (manual)
~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    $ sudo php-cs-fixer self-update

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    $ brew upgrade php-cs-fixer

Usage
-----

The ``fix`` command tries to fix as much coding standards
problems as possible on a given file or directory:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/dir
    php php-cs-fixer.phar fix /path/to/file

The ``--level`` option limits the fixers to apply on the
project:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/project --level=psr0
    php php-cs-fixer.phar fix /path/to/project --level=psr1
    php php-cs-fixer.phar fix /path/to/project --level=psr2
    php php-cs-fixer.phar fix /path/to/project --level=symfony

By default, all PSR-2 fixers and some additional ones are run. The "contrib
level" fixers cannot be enabled via this option; you should instead set them
manually by their name via the ``--fixers`` option.

The ``--fixers`` option lets you choose the exact fixers to
apply (the fixer names must be separated by a comma):

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/dir --fixers=linefeed,short_tag,indentation

You can also blacklist the fixers you don't want if this is more convenient,
using ``-name``:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/dir --fixers=-short_tag,-indentation

A combination of ``--dry-run``, ``--verbose`` and ``--diff`` will
display summary of proposed fixes, leaving your files unchanged.

The command can also read from standard input, in which case it won't
automatically fix anything:

.. code-block:: bash

    cat foo.php | php php-cs-fixer.phar fix -v --diff -

Choose from the list of available fixers:

* **psr0** [PSR-0] Classes must be in a path that
               matches their namespace, be at least
               one namespace deep, and the class name
               should match the file name.

* **encoding** [PSR-1] PHP code MUST use only UTF-8
               without BOM (remove BOM).

* **short_tag** [PSR-1] PHP code must use the long
               <?php ?> tags or the short-echo <?= ?>
               tags; it must not use the other tag
               variations.

* **braces** [PSR-2] The body of each structure MUST
               be enclosed by braces. Braces should be
               properly placed. Body of braces should
               be properly indented.

* **elseif** [PSR-2] The keyword elseif should be
               used instead of else if so that all
               control keywords looks like single
               words.

* **eof_ending** [PSR-2] A file must always end with an
               empty line feed.

* **function_declaration** [PSR-2] Spaces should be properly
               placed in a function declaration.

* **indentation** [PSR-2] Code MUST use an indent of 4
               spaces, and MUST NOT use tabs for
               indenting.

* **line_after_namespace** [PSR-2] There MUST be one blank line
               after the namespace declaration.

* **linefeed** [PSR-2] All PHP files must use the Unix
               LF (linefeed) line ending.

* **lowercase_constants** [PSR-2] The PHP constants true, false,
               and null MUST be in lower case.

* **lowercase_keywords** [PSR-2] PHP keywords MUST be in lower
               case.

* **multiple_use** [PSR-2] There MUST be one use keyword
               per declaration.

* **php_closing_tag** [PSR-2] The closing ?> tag MUST be
               omitted from files containing only PHP.

* **trailing_spaces** [PSR-2] Remove trailing whitespace at
               the end of non-blank lines.

* **visibility** [PSR-2] Visibility MUST be declared on
               all properties and methods; abstract
               and final MUST be declared before the
               visibility; static MUST be declared
               after the visibility.

* **concat_without_spaces** [symfony] Concatenation should be used
               without spaces.

* **duplicate_semicolon** [symfony] Remove duplicated semicolons.

* **extra_empty_lines** [symfony] Removes extra empty lines.

* **include** [symfony] Include and file path should
               be divided with a single space. File
               path should not be placed under
               brackets.

* **multiline_array_trailing_comma** [symfony] PHP multi-line arrays should
               have a trailing comma.

* **namespace_no_leading_whitespace** [symfony] The namespace declaration
               line shouldn't contain leading
               whitespace.

* **new_with_braces** [symfony] All instances created with
               new keyword must be followed by braces.

* **object_operator** [symfony] There should not be space
               before or after object
               T_OBJECT_OPERATOR.

* **operators_spaces** [symfony] Operators should be arounded
               by at least one space.

* **phpdoc_params** [symfony] All items of the @param
               phpdoc tags must be aligned vertically.

* **return** [symfony] An empty line feed should
               precede a return statement.

* **single_array_no_trailing_comma** [symfony] PHP single-line arrays should
               not have trailing comma.

* **spaces_before_semicolon** [symfony] Single-line whitespace before
               closing semicolon are prohibited.

* **spaces_cast** [symfony] A single space should be
               between cast and variable.

* **standardize_not_equal** [symfony] Replace all <> with !=.

* **ternary_spaces** [symfony] Standardize spaces around
               ternary operator.

* **unused_use** [symfony] Unused use statements must be
               removed.

* **whitespacy_lines** [symfony] Remove trailing whitespace at
               the end of blank lines.

* **concat_with_spaces** [contrib] Concatenation should be used
               with at least one whitespace around.

* **multiline_spaces_before_semicolon** [contrib] Multi-line whitespace before
               closing semicolon are prohibited.

* **ordered_use** [contrib] Ordering use statements.

* **short_array_syntax** [contrib] PHP array's should use the
               PHP 5.4 short-syntax.

* **strict** [contrib] Comparison should be strict.
               Warning! This could change code
               behavior.


The ``--config`` option customizes the files to analyse, based
on some well-known directory structures:

.. code-block:: bash

    # For the Symfony 2.3+ branch
    php php-cs-fixer.phar fix /path/to/sf23 --config=sf23

Choose from the list of available configurations:

* **default** A default configuration

* **magento** The configuration for a Magento application

* **sf23**    The configuration for the Symfony 2.3+ branch

The ``--dry-run`` option displays the files that need to be
fixed but without actually modifying them:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/code --dry-run

Instead of using command line options to customize the fixer, you can save the
configuration in a ``.php_cs`` file in the root directory of
your project. The file must return an instance of
`Symfony\CS\ConfigInterface`, which lets you configure the fixers, the files,
and directories that need to be analyzed:

.. code-block:: php

    <?php

    $finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->fixers(array('indentation', 'elseif'))
        ->finder($finder)
    ;

You may also use a blacklist for the Fixers instead of the above shown whitelist approach.
The following example shows how to use all Fixers but the `psr0` fixer.
Note the additional ``-`` in front of the Fixer name.

.. code-block:: php

    <?php

    $finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->fixers(array('-psr0'))
        ->finder($finder)
    ;

With the ``--config-file`` option you can specify the path to the
``.php_cs`` file.

Helpers
-------

Dedicated plugins exist for:

* `Vim`_
* `Sublime Text`_
* `NetBeans`_
* `PhpStorm`_

Contribute
----------

The tool comes with quite a few built-in fixers and finders, but everyone is
more than welcome to `contribute`_ more of them.

Fixers
~~~~~~

A *fixer* is a class that tries to fix one CS issue (a ``Fixer`` class must
implement ``FixerInterface``).

Configs
~~~~~~~

A *config* knows about the CS level and the files and directories that must be
scanned by the tool when run in the directory of your project. It is useful for
projects that follow a well-known directory structures (like for Symfony
projects for instance).

.. _php-cs-fixer.phar: http://get.sensiolabs.org/php-cs-fixer.phar
.. _Vim:               https://github.com/stephpy/vim-php-cs-fixer
.. _Sublime Text:      https://github.com/benmatselby/sublime-phpcs
.. _NetBeans:          http://plugins.netbeans.org/plugin/49042/php-cs-fixer
.. _PhpStorm:          http://arnolog.net/post/92715936483/use-fabpots-php-cs-fixer-tool-in-phpstorm-in-2-steps
.. _contribute:        https://github.com/fabpot/php-cs-fixer/blob/master/CONTRIBUTING.md
