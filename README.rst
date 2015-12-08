PHP Coding Standards Fixer
==========================

The PHP Coding Standards Fixer tool fixes *most* issues in your code when you
want to follow the PHP coding standards as defined in the PSR-1 and PSR-2
documents and many more.

If you are already using a linter to identify coding standards problems in your
code, you know that fixing them by hand is tedious, especially on large
projects. This tool does not only detect them, but also fixes them for you.

Requirements
------------

PHP needs to be a minimum version of PHP 5.3.6.

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

    $ wget http://get.sensiolabs.org/php-cs-fixer.phar -O php-cs-fixer

or with curl:

.. code-block:: bash

    $ curl http://get.sensiolabs.org/php-cs-fixer.phar -o php-cs-fixer

then:

.. code-block:: bash

    $ sudo chmod a+x php-cs-fixer
    $ sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer

Then, just run ``php-cs-fixer``.

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

To install PHP-CS-Fixer, install Composer and issue the following command:

.. code-block:: bash

    $ ./composer.phar global require fabpot/php-cs-fixer

Then make sure you have ``~/.composer/vendor/bin`` in your ``PATH`` and
you're good to go:

.. code-block:: bash

    export PATH="$PATH:$HOME/.composer/vendor/bin"

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

PHP-CS-Fixer is part of the homebrew-php project. Follow the installation
instructions at https://github.com/homebrew/homebrew-php if you don't
already have it.

.. code-block:: bash

    $ brew install homebrew/php/php-cs-fixer

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

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    $ ./composer.phar global update fabpot/php-cs-fixer

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    $ brew upgrade php-cs-fixer

Usage
-----

The ``fix`` command tries to fix as much coding standards
problems as possible on a given file or files in a given directory and its subdirectories:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/dir
    php php-cs-fixer.phar fix /path/to/file

The ``--format`` option can be used to set the output format of the results; ``txt`` (default one), ``xml`` or ``json``.

The ``--verbose`` option will show the applied fixers. When using the ``txt`` format it will also displays progress notifications.

The ``--rules`` option limits the rules to apply on the
project:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/project --rules=@PSR2

By default, all PSR fixers are run.

The ``--rules`` option lets you choose the exact fixers to
apply (the fixer names must be separated by a comma):

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/dir --rules=linefeed,short_tag,indentation

You can also blacklist the fixers you don't want by placing a dash in front of the fixer name, if this is more convenient,
using ``-name_of_fixer``:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/dir --rules=-short_tag,-indentation

When using combinations of exact and blacklist fixers, applying exact fixers along with above blacklisted results:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/project --rules=@Symfony,-@PSR1,-return,strict

A combination of ``--dry-run`` and ``--diff`` will
display a summary of proposed fixes, leaving your files unchanged.

The ``--allow-risky`` option allows you to set whether riskys fixer may run. Default value is taken from config file.
Risky fixer is a fixer, which could change code behaviour. By default no risky fixers are run.

The command can also read from standard input, in which case it won't
automatically fix anything:

.. code-block:: bash

    cat foo.php | php php-cs-fixer.phar fix --diff -

Choose from the list of available fixers:

* **alias_functions** [@Symfony]
                        Master functions shall be used
                        instead of aliases.

* **align_double_arrow**
                        Align double arrow symbols in
                        consecutive lines.

* **align_equals**
                        Align equals symbols in
                        consecutive lines.

* **array_element_no_space_before_comma** [@Symfony]
                        In array declaration, there
                        MUST NOT be a whitespace
                        before each comma.

* **array_element_white_space_after_comma** [@Symfony]
                        In array declaration, there
                        MUST be a whitespace after
                        each comma.

* **blankline_after_open_tag** [@Symfony]
                        Ensure there is no code on the
                        same line as the PHP open tag
                        and it is followed by a
                        blankline.

* **braces** [@PSR2, @Symfony]
                        The body of each structure
                        MUST be enclosed by braces.
                        Braces should be properly
                        placed. Body of braces should
                        be properly indented.

* **class_definition** [@PSR2, @Symfony]
                        Whitespace around the key
                        words of a class, trait or
                        interfaces definition should
                        be one space.

* **concat_with_spaces**
                        Concatenation should be used
                        with at least one whitespace
                        around.

* **concat_without_spaces** [@Symfony]
                        Concatenation should be used
                        without spaces.

* **double_arrow_multiline_whitespaces** [@Symfony]
                        Operator => should not be
                        surrounded by multi-line
                        whitespaces.

* **duplicate_semicolon** [@Symfony]
                        Remove duplicated semicolons.

* **echo_to_print**
                        Converts echo language
                        construct to print if
                        possible.

* **elseif** [@PSR2, @Symfony]
                        The keyword elseif should be
                        used instead of else if so
                        that all control keywords look
                        like single words.

* **empty_return** [@Symfony]
                        A return statement wishing to
                        return nothing should be
                        simply "return".

* **encoding** [@PSR1, @PSR2, @Symfony]
                        PHP code MUST use only UTF-8
                        without BOM (remove BOM).

* **eof_ending** [@PSR2, @Symfony]
                        A file must always end with a
                        single empty line feed.

* **ereg_to_preg**
                        Replace deprecated ereg
                        regular expression functions
                        with preg. (Risky fixer!)

* **extra_empty_lines** [@Symfony]
                        Removes extra blank lines
                        and/or blank lines following
                        configuration.

* **function_call_space** [@PSR2, @Symfony]
                        When making a method or
                        function call, there MUST NOT
                        be a space between the method
                        or function name and the
                        opening parenthesis.

* **function_declaration** [@PSR2, @Symfony]
                        Spaces should be properly
                        placed in a function
                        declaration.

* **function_typehint_space** [@Symfony]
                        Add missing space between
                        function's argument and its
                        typehint.

* **header_comment**
                        Add, replace or remove header
                        comment.

* **include** [@Symfony]
                        Include and file path should
                        be divided with a single
                        space. File path should not be
                        placed under brackets.

* **indentation** [@PSR2, @Symfony]
                        Code MUST use an indent of 4
                        spaces, and MUST NOT use tabs
                        for indenting.

* **line_after_namespace** [@PSR2, @Symfony]
                        There MUST be one blank line
                        after the namespace
                        declaration.

* **linefeed** [@PSR2, @Symfony]
                        All PHP files must use the
                        Unix LF (linefeed) line
                        ending.

* **list_commas** [@Symfony]
                        Remove trailing commas in list
                        function calls.

* **logical_not_operators_with_spaces**
                        Logical NOT operators (!)
                        should have leading and
                        trailing whitespaces.

* **logical_not_operators_with_successor_space**
                        Logical NOT operators (!)
                        should have one trailing
                        whitespace.

* **long_array_syntax**
                        Arrays should use the long
                        syntax.

* **lowercase_constants** [@PSR2, @Symfony]
                        The PHP constants true, false,
                        and null MUST be in lower
                        case.

* **lowercase_keywords** [@PSR2, @Symfony]
                        PHP keywords MUST be in lower
                        case.

* **method_argument_default_value** [@Symfony]
                        In method arguments there must
                        not be arguments with default
                        values before non-default
                        ones.

* **method_argument_space** [@PSR2, @Symfony]
                        In method arguments and method
                        call, there MUST NOT be a
                        space before each comma and
                        there MUST be one space after
                        each comma.

* **method_separation** [@Symfony]
                        Methods must be separated with
                        one blank line.

* **multiline_array_trailing_comma** [@Symfony]
                        PHP multi-line arrays should
                        have a trailing comma.

* **multiline_spaces_before_semicolon**
                        Multi-line whitespace before
                        closing semicolon are
                        prohibited.

* **multiple_use** [@PSR2, @Symfony]
                        There MUST be one use keyword
                        per declaration.

* **namespace_no_leading_whitespace** [@Symfony]
                        The namespace declaration line
                        shouldn't contain leading
                        whitespace.

* **new_with_braces** [@Symfony]
                        All instances created with new
                        keyword must be followed by
                        braces.

* **newline_after_open_tag**
                        Ensure there is no code on the
                        same line as the PHP open tag.

* **no_blank_lines_after_class_opening** [@Symfony]
                        There should be no empty lines
                        after class opening brace.

* **no_blank_lines_before_namespace**
                        There should be no blank lines
                        before a namespace
                        declaration.

* **no_empty_lines_after_phpdocs** [@Symfony]
                        There should not be blank
                        lines between docblock and the
                        documented element.

* **object_operator** [@Symfony]
                        There should not be space
                        before or after object
                        T_OBJECT_OPERATOR.

* **operators_spaces** [@Symfony]
                        Binary operators should be
                        surrounded by at least one
                        space.

* **ordered_use**
                        Ordering use statements.

* **parenthesis** [@PSR2, @Symfony]
                        There MUST NOT be a space
                        after the opening parenthesis.
                        There MUST NOT be a space
                        before the closing
                        parenthesis.

* **php4_constructor**
                        Convert PHP4-style
                        constructors to __construct.
                        (Risky fixer!)

* **php_closing_tag** [@PSR2, @Symfony]
                        The closing ?> tag MUST be
                        omitted from files containing
                        only PHP.

* **php_unit_construct**
                        PHPUnit assertion method calls
                        like "->assertSame(true,
                        $foo)" should be written with
                        dedicated method like
                        "->assertTrue($foo)". (Risky
                        fixer!)

* **php_unit_strict**
                        PHPUnit methods like
                        "assertSame" should be used
                        instead of "assertEquals".
                        (Risky fixer!)

* **phpdoc_align** [@Symfony]
                        All items of the @param,
                        @throws, @return, @var, and
                        @type phpdoc tags must be
                        aligned vertically.

* **phpdoc_indent** [@Symfony]
                        Docblocks should have the same
                        indentation as the documented
                        subject.

* **phpdoc_inline_tag** [@Symfony]
                        Fix PHPDoc inline tags, make
                        inheritdoc always inline.

* **phpdoc_no_access** [@Symfony]
                        @access annotations should be
                        omitted from phpdocs.

* **phpdoc_no_empty_return** [@Symfony]
                        @return void and @return null
                        annotations should be omitted
                        from phpdocs.

* **phpdoc_no_package** [@Symfony]
                        @package and @subpackage
                        annotations should be omitted
                        from phpdocs.

* **phpdoc_order**
                        Annotations in phpdocs should
                        be ordered so that param
                        annotations come first, then
                        throws annotations, then
                        return annotations.

* **phpdoc_property**
                        @property tags should be used
                        rather than other variants.

* **phpdoc_scalar** [@Symfony]
                        Scalar types should always be
                        written in the same form.
                        "int", not "integer"; "bool",
                        not "boolean"; "float", not
                        "real" or "double".

* **phpdoc_separation** [@Symfony]
                        Annotations in phpdocs should
                        be grouped together so that
                        annotations of the same type
                        immediately follow each other,
                        and annotations of a different
                        type are separated by a single
                        blank line.

* **phpdoc_summary** [@Symfony]
                        Phpdocs summary should end in
                        either a full stop,
                        exclamation mark, or question
                        mark.

* **phpdoc_to_comment** [@Symfony]
                        Docblocks should only be used
                        on structural elements.

* **phpdoc_trim** [@Symfony]
                        Phpdocs should start and end
                        with content, excluding the
                        very first and last line of
                        the docblocks.

* **phpdoc_type_to_var** [@Symfony]
                        @type should always be written
                        as @var.

* **phpdoc_types** [@Symfony]
                        The correct case must be used
                        for standard PHP types in
                        phpdoc.

* **phpdoc_var_to_type**
                        @var should always be written
                        as @type.

* **phpdoc_var_without_name** [@Symfony]
                        @var and @type annotations
                        should not contain the
                        variable name.

* **pre_increment** [@Symfony]
                        Pre
                        incrementation/decrementation
                        should be used if possible.

* **print_to_echo** [@Symfony]
                        Converts print language
                        construct to echo if possible.

* **psr0**
                        Classes must be in a path that
                        matches their namespace, be at
                        least one namespace deep and
                        the class name should match
                        the file name. (Risky fixer!)

* **remove_leading_slash_use** [@Symfony]
                        Remove leading slashes in use
                        clauses.

* **remove_lines_between_uses** [@Symfony]
                        Removes line breaks between
                        use statements.

* **return** [@Symfony]
                        An empty line feed should
                        precede a return statement.

* **self_accessor** [@Symfony]
                        Inside a classy element "self"
                        should be preferred to the
                        class name itself.

* **short_array_syntax**
                        PHP arrays should use the PHP
                        5.4 short-syntax.

* **short_bool_cast** [@Symfony]
                        Short cast bool using double
                        exclamation mark should not be
                        used.

* **short_echo_tag**
                        Replace short-echo <?= with
                        long format <?php echo syntax.

* **short_tag** [@PSR1, @PSR2, @Symfony]
                        PHP code must use the long
                        <?php ?> tags or the
                        short-echo <?= ?> tags; it
                        must not use the other tag
                        variations.

* **single_array_no_trailing_comma** [@Symfony]
                        PHP single-line arrays should
                        not have trailing comma.

* **single_blank_line_before_namespace** [@Symfony]
                        There should be exactly one
                        blank line before a namespace
                        declaration.

* **single_line_after_imports** [@PSR2, @Symfony]
                        Each namespace use MUST go on
                        its own line and there MUST be
                        one blank line after the use
                        statements block.

* **single_quote** [@Symfony]
                        Convert double quotes to
                        single quotes for simple
                        strings.

* **spaces_before_semicolon** [@Symfony]
                        Single-line whitespace before
                        closing semicolon are
                        prohibited.

* **spaces_cast** [@Symfony]
                        A single space should be
                        between cast and variable.

* **standardize_not_equal** [@Symfony]
                        Replace all <> with !=.

* **strict**
                        Comparison should be strict.
                        (Risky fixer!)

* **strict_param**
                        Functions should be used with
                        $strict param. (Risky fixer!)

* **ternary_spaces** [@Symfony]
                        Standardize spaces around
                        ternary operator.

* **trailing_spaces** [@PSR2, @Symfony]
                        Remove trailing whitespace at
                        the end of non-blank lines.

* **trim_array_spaces** [@Symfony]
                        Arrays should be formatted
                        like function/method
                        arguments, without leading or
                        trailing single line space.

* **unalign_double_arrow** [@Symfony]
                        Unalign double arrow symbols.

* **unalign_equals** [@Symfony]
                        Unalign equals symbols.

* **unary_operators_spaces** [@Symfony]
                        Unary operators should be
                        placed adjacent to their
                        operands.

* **unneeded_control_parentheses** [@Symfony]
                        Removes unneeded parentheses
                        around control statements.

* **unused_use** [@Symfony]
                        Unused use statements must be
                        removed.

* **visibility** [@PSR2, @Symfony]
                        Visibility MUST be declared on
                        all properties and methods;
                        abstract and final MUST be
                        declared before the
                        visibility; static MUST be
                        declared after the visibility.

* **whitespacy_lines** [@Symfony]
                        Remove trailing whitespace at
                        the end of blank lines.


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
project configuration in a ``.php_cs.dist`` file in the root directory
of your project. The file must return an instance of ``Symfony\CS\ConfigInterface``,
which lets you configure the rules, the files and directories that
need to be analyzed. You may also create ``.php_cs`` file, which is
the local configuration that will be used instead of the project configuration. It
is a good practice to add that file into your ``.gitignore`` file.
With the ``--config-file`` option you can specify the path to the
``.php_cs`` file.

The example below will add two fixers to the default list of PSR2 set fixers:

.. code-block:: php

    <?php

    $finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->setRules(array(
            '@PSR2' => true,
            'strict_param' => true,
            'short_array_syntax' => true,
        ))
        ->finder($finder)
    ;

You may also use a blacklist for the Fixers instead of the above shown whitelist approach.
The following example shows how to use all ``Symfony`` Fixers but the ``short_tag`` Fixer.

.. code-block:: php

    <?php

    $finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->setRules(array(
            '@Symfony' => true,
            'short_tag' => false,
        ))
        ->finder($finder)
    ;

By using ``--using-cache`` option with yes or no you can set if the caching
mechanism should be used.

Caching
-------

The caching mechanism is enabled by default. This will speed up further runs by
fixing only files that were modified since the last run. The tool will fix all
files if the tool version has changed or the list of fixers has changed.
Cache is supported only for tool downloaded as phar file or installed via
composer.

Cache can be disabled via ``--using-cache`` option or config file:

.. code-block:: php

    <?php

    return Symfony\CS\Config\Config::create()
        ->setUsingCache(false)
    ;

Cache file can be specified via ``--cache-file`` option or config file:

.. code-block:: php

    <?php

    return Symfony\CS\Config\Config::create()
        ->setCacheFile(__DIR__.'/.php_cs.cache')
    ;

Using PHP CS Fixer on Travis
----------------------------

Require ``fabpot/php-cs-fixer`` as a `dev`` dependency:

.. code-block:: bash

    $ ./composer.phar require --dev fabpot/php-cs-fixer

Create a build file to run ``php-cs-fixer`` on Travis. It's advisable to create a dedicated directory
for PHP CS Fixer cache files and have Travis cache it between builds.

.. code-block:: yaml

    language: php
    php:
        - 5.5
    sudo: false
    cache:
        directories:
            - "$HOME/.composer/cache"
            - "$HOME/.php-cs-fixer"
    before_script:
        - mkdir -p "$HOME/.php-cs-fixer"
    script:
        - vendor/bin/php-cs-fixer fix --cache-file "$HOME/.php-cs-fixer/.php_cs.cache" --dry-run --diff --verbose

Note: This will only trigger a build if you have a subscription for Travis
or are using their free open source plan.

Helpers
-------

Dedicated plugins exist for:

* `Atom`_
* `NetBeans`_
* `PhpStorm`_
* `Sublime Text`_
* `Vim`_

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

A *config* knows about the CS rules and the files and directories that must be
scanned by the tool when run in the directory of your project. It is useful for
projects that follow a well-known directory structures (like for Symfony
projects for instance).

.. _php-cs-fixer.phar: http://get.sensiolabs.org/php-cs-fixer.phar
.. _Atom:              https://github.com/Glavin001/atom-beautify
.. _NetBeans:          http://plugins.netbeans.org/plugin/49042/php-cs-fixer
.. _PhpStorm:          http://tzfrs.de/2015/01/automatically-format-code-to-match-psr-standards-with-phpstorm
.. _Sublime Text:      https://github.com/benmatselby/sublime-phpcs
.. _Vim:               https://github.com/stephpy/vim-php-cs-fixer
.. _contribute:        https://github.com/FriendsOfPhp/php-cs-fixer/blob/master/CONTRIBUTING.md
