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

When using combination with exact and blacklist fixers, apply exact fixers along with above blacklisted result:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/dir --fixers=linefeed,-short_tag

A combination of ``--dry-run`` and ``--diff`` will
display summary of proposed fixes, leaving your files unchanged.

The command can also read from standard input, in which case it won't
automatically fix anything:

.. code-block:: bash

    cat foo.php | php php-cs-fixer.phar fix --diff -

Choose from the list of available fixers:

====================================    ========================================    ============
Rule                                    Description                                 Group
====================================    ========================================    ============
psr-0                                   Classes must be in a path that              PSR-0
                                        matches their namespace, be at least
                                        one namespace deep, and the class name 
                                        should match the file name.
                                        
encoding                                PHP code MUST use only UTF-8                PSR-1
                                        without BOM (remove BOM).
                                        
short_tag                               PHP code must use the long                  PSR-1
                                        <?php ?> tags or the short-echo <?= ?>
                                        tags; it must not use the other tag
                                        variations.

braces                                  The body of each structure MUST             PSR-2
                                        be enclosed by braces. Braces should be
                                        properly placed. Body of braces should
                                        be properly indented.

elseif                                  The keyword elseif should be                PSR-2
                                        used instead of else if so that all
                                        control keywords looks like single
                                        words.

eof_ending                              A file must always end with an              PSR-2
                                        empty line feed.

function_call_space                     When making a method or                     PSR-2
                                        function call, there MUST NOT be a
                                        space between the method or function
                                        name and the opening parenthesis.

function_declaration                    Spaces should be properly                   PSR-2
                                        placed in a function declaration.

indentation                             Code MUST use an indent of 4                PSR-2
                                        spaces, and MUST NOT use tabs for
                                        identing.

line_after_namespace                    There MUST be one blank line                PSR-2
                                        after the namespace declaration.

linefeed                                All PHP files must use the Unix             PSR-2
                                        LF (linefeed) line ending.

lowercase_constants                     The PHP constants true, false,              PSR-2
                                        and null MUST be in lower case.

lowercase_keywords                      PHP keywords MUST be in lower               PSR-2
                                        case.

method_argument_space                   In method arguments and method              PSR-2
                                        call, there MUST NOT be a space before
                                        each comma and there MUST be one space
                                        after each comma.

multiple_use                            There MUST be one use keyword               PSR-2
                                        per declaration.

parenthesis                             There MUST NOT be a space after             PSR-2
                                        the opening parenthesis. There MUST NOT
                                        be a space before the closing
                                        parenthesis.

php_closing_tag                         The closing ?> tag MUST be                  PSR-2
                                        omitted from files containing only PHP.

trailing_spaces                         Remove trailing whitespace at               PSR-2
                                        the end of non-blank lines.

visibility                              Visibility MUST be declared on              PSR-2
                                        all properties and methods; abstract
                                        and final MUST be declared before the
                                        visibility; static MUST be declared
                                        after the visibility.

concat_without_spaces                   Concatenation should be used                symfony
                                        without spaces.

duplicate_semicolon                     Remove duplicated semicolons.               symfony

extra_empty_lines                       Removes extra empty lines.                  symfony

include                                 Include and file path should                symfony
                                        be divided with a single space. File
                                        path should not be placed under
                                        brackets.

multiline_array_trailing_comma          PHP multi-line arrays should                symfony
                                        have a trailing comma.

namespace_no_leading_whitespace         The namespace declaration                   symfony
                                        line shouldn't contain leading
                                        whitespace.

new_with_braces                         All instances created with                  symfony
                                        new keyword must be followed by braces.

object_operator                         There should not be space                   symfony
                                        before or after object
                                        T_OBJECT_OPERATOR.

operators_spaces                        Operators should be arounded                symfony
                                        by at least one space.

phpdoc_params                           All items of the @param                     symfony
                                        phpdoc tags must be aligned vertically.

remove_lines_between_uses               Removes line breaks between                 symfony
                                        use statements.

return                                  An empty line feed should                   symfony
                                        precede a return statement.

single_array_no_trailing_comma          PHP single-line arrays should               symfony
                                        not have trailing comma.

spaces_before_semicolon                 Single-line whitespace before               symfony
                                        closing semicolon are prohibited.

spaces_cast                             A single space should be                    symfony
                                        between cast and variable.                         

standardize_not_equal                   Replace all <> with !=.                     symfony

ternary_spaces                          Standardize spaces around                   symfony
                                        ternary operator.

unused_use                              Unused use statements must be               symfony
                                        removed.

whitespacy_lines                        Remove trailing whitespace at               symfony
                                        the end of blank lines.

yoda_conditions                         Comparisons should be done                  symfony
                                        using Yoda conditions.
                                        
align_double_arrow                      Align double arrow symbols in               contrib
                                        consecutive lines.

align_equals                            Align equals symbols in                     contrib
                                        consecutive lines.

concat_with_spaces                      Concatenation should be used                contrib
                                        with at least one whitespace around.

multiline_spaces_before_semicolon       Multi-line whitespace before                contrib
                                        closing semicolon are prohibited.

ordered_use                             Ordering use statements.                    contrib

short_array_syntax                      PHP array's should use the                  contrib
                                        PHP 5.4 short-syntax.

strict                                  Comparison should be strict.                contrib
                                        Warning! This could change code
                                        behavior.

strict_param                            Functions should be used with               contrib
                                        $strict param. Warning! This could
                                        change code behavior.

====================================    ========================================    ============


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
`Symfony\CS\ConfigInterface`, which lets you configure the fixers, the level, the files,
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
The following example shows how to use all ``symfony`` Fixers but the `psr0` fixer.
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

The ``symfony`` level is set by default, you can also change the default level:

.. code-block:: php

    <?php

    return Symfony\CS\Config\Config::create()
        ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ;

In combination with these config and command line options, you can choose various usage.

For example, default level is ``symfony``, but if you also don't want to use
the ``psr0`` fixer, you can specify the ``--fixers="-psr0"`` option.

But if you use the ``--fixers`` option with only exact fixers,
only those exact fixers are enabled whether or not level is set.

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
