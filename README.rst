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
    # With a specific version
    $ wget http://get.sensiolabs.org/php-cs-fixer-v1.11.phar -O php-cs-fixer

or with curl:

.. code-block:: bash

    $ curl http://get.sensiolabs.org/php-cs-fixer.phar -o php-cs-fixer
    # With a specific version
    $ curl http://get.sensiolabs.org/php-cs-fixer-v1.11.phar -o php-cs-fixer

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

The ``--format`` option for the output format. Supported formats are ``txt`` (default one), ``json`` and ``xml``.

The ``--verbose`` option will show the applied fixers. When using the ``txt`` format it will also displays progress notifications.

The ``--rules`` option limits the rules to apply on the
project:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/project --rules=@PSR2

By default, all PSR fixers are run.

The ``--rules`` option lets you choose the exact fixers to
apply (the fixer names must be separated by a comma):

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/dir --rules=unix_line_endings,full_opening_tag,no_tab_indentation

You can also blacklist the fixers you don't want by placing a dash in front of the fixer name, if this is more convenient,
using ``-name_of_fixer``:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/dir --rules=-full_opening_tag,-no_tab_indentation

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

* **align_double_arrow**
                        Align double arrow symbols in
                        consecutive lines.

* **align_equals**
                        Align equals symbols in
                        consecutive lines.

* **binary_operator_spaces** [@Symfony]
                        Binary operators should be
                        surrounded by at least one
                        space.

* **blank_line_after_namespace** [@PSR2, @Symfony]
                        There MUST be one blank line
                        after the namespace
                        declaration.

* **blank_line_after_opening_tag** [@Symfony]
                        Ensure there is no code on the
                        same line as the PHP open tag
                        and it is followed by a
                        blankline.

* **blank_line_before_return** [@Symfony]
                        An empty line feed should
                        precede a return statement.

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

* **double_arrow_no_multiline_whitespace** [@Symfony]
                        Operator => should not be
                        surrounded by multi-line
                        whitespaces.

* **echo_to_print**
                        Converts echo language
                        construct to print if
                        possible.

* **elseif** [@PSR2, @Symfony]
                        The keyword elseif should be
                        used instead of else if so
                        that all control keywords look
                        like single words.

* **encoding** [@PSR1, @PSR2, @Symfony]
                        PHP code MUST use only UTF-8
                        without BOM (remove BOM).

* **ereg_to_preg**
                        Replace deprecated ereg
                        regular expression functions
                        with preg. (Risky fixer!)

* **full_opening_tag** [@PSR1, @PSR2, @Symfony]
                        PHP code must use the long
                        <?php ?> tags or the
                        short-echo <?= ?> tags; it
                        must not use the other tag
                        variations.

* **function_declaration** [@PSR2, @Symfony]
                        Spaces should be properly
                        placed in a function
                        declaration.

* **function_typehint_space** [@Symfony]
                        Add missing space between
                        function's argument and its
                        typehint.

* **hash_to_slash_comment** [@Symfony]
                        Single line comments should
                        use double slashes (//) and
                        not hash (#).

* **header_comment**
                        Add, replace or remove header
                        comment.

* **heredoc_to_nowdoc** [@Symfony]
                        Convert heredoc to nowdoc if
                        possible.

* **include** [@Symfony]
                        Include/Require and file path
                        should be divided with a
                        single space. File path should
                        not be placed under brackets.

* **linebreak_after_opening_tag**
                        Ensure there is no code on the
                        same line as the PHP open tag.

* **long_array_syntax**
                        Arrays should use the long
                        syntax.

* **lowercase_cast** [@Symfony]
                        Cast should be written in
                        lower case.

* **lowercase_constants** [@PSR2, @Symfony]
                        The PHP constants true, false,
                        and null MUST be in lower
                        case.

* **lowercase_keywords** [@PSR2, @Symfony]
                        PHP keywords MUST be in lower
                        case.

* **method_argument_space** [@PSR2, @Symfony]
                        In method arguments and method
                        call, there MUST NOT be a
                        space before each comma and
                        there MUST be one space after
                        each comma.

* **method_separation** [@Symfony]
                        Methods must be separated with
                        one blank line.

* **native_function_casing** [@Symfony]
                        Function defined by PHP should
                        be called using the correct
                        casing.

* **new_with_braces** [@Symfony]
                        All instances created with new
                        keyword must be followed by
                        braces.

* **no_alias_functions** [@Symfony]
                        Master functions shall be used
                        instead of aliases.

* **no_blank_lines_after_class_opening** [@Symfony]
                        There should be no empty lines
                        after class opening brace.

* **no_blank_lines_after_phpdoc** [@Symfony]
                        There should not be blank
                        lines between docblock and the
                        documented element.

* **no_blank_lines_before_namespace**
                        There should be no blank lines
                        before a namespace
                        declaration.

* **no_blank_lines_between_uses** [@Symfony]
                        Removes line breaks between
                        use statements.

* **no_closing_tag** [@PSR2, @Symfony]
                        The closing ?> tag MUST be
                        omitted from files containing
                        only PHP.

* **no_duplicate_semicolons** [@Symfony]
                        Remove duplicated semicolons.

* **no_extra_consecutive_blank_lines** [@Symfony]
                        Removes extra blank lines
                        and/or blank lines following
                        configuration.

* **no_leading_import_slash** [@Symfony]
                        Remove leading slashes in use
                        clauses.

* **no_leading_namespace_whitespace** [@Symfony]
                        The namespace declaration line
                        shouldn't contain leading
                        whitespace.

* **no_multiline_whitespace_before_semicolons**
                        Multi-line whitespace before
                        closing semicolon are
                        prohibited.

* **no_php4_constructor**
                        Convert PHP4-style
                        constructors to __construct.
                        (Risky fixer!)

* **no_short_bool_cast** [@Symfony]
                        Short cast bool using double
                        exclamation mark should not be
                        used.

* **no_short_echo_tag**
                        Replace short-echo <?= with
                        long format <?php echo syntax.

* **no_singleline_whitespace_before_semicolons** [@Symfony]
                        Single-line whitespace before
                        closing semicolon are
                        prohibited.

* **no_spaces_after_function_name** [@PSR2, @Symfony]
                        When making a method or
                        function call, there MUST NOT
                        be a space between the method
                        or function name and the
                        opening parenthesis.

* **no_spaces_inside_parenthesis** [@PSR2, @Symfony]
                        There MUST NOT be a space
                        after the opening parenthesis.
                        There MUST NOT be a space
                        before the closing
                        parenthesis.

* **no_tab_indentation** [@PSR2, @Symfony]
                        Code MUST use an indent of 4
                        spaces, and MUST NOT use tabs
                        for indenting.

* **no_trailing_comma_in_list_call** [@Symfony]
                        Remove trailing commas in list
                        function calls.

* **no_trailing_comma_in_singleline_array** [@Symfony]
                        PHP single-line arrays should
                        not have trailing comma.

* **no_trailing_whitespace** [@PSR2, @Symfony]
                        Remove trailing whitespace at
                        the end of non-blank lines.

* **no_unneeded_control_parentheses** [@Symfony]
                        Removes unneeded parentheses
                        around control statements.

* **no_unreachable_default_argument_value** [@Symfony]
                        In method arguments there must
                        not be arguments with default
                        values before non-default
                        ones.

* **no_unused_imports** [@Symfony]
                        Unused use statements must be
                        removed.

* **no_whitespace_before_comma_in_array** [@Symfony]
                        In array declaration, there
                        MUST NOT be a whitespace
                        before each comma.

* **not_operator_with_space**
                        Logical NOT operators (!)
                        should have leading and
                        trailing whitespaces.

* **not_operator_with_successor_space**
                        Logical NOT operators (!)
                        should have one trailing
                        whitespace.

* **object_operator_without_whitespace** [@Symfony]
                        There should not be space
                        before or after object
                        T_OBJECT_OPERATOR.

* **ordered_imports**
                        Ordering use statements.

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

* **self_accessor** [@Symfony]
                        Inside a classy element "self"
                        should be preferred to the
                        class name itself.

* **short_array_syntax**
                        PHP arrays should use the PHP
                        5.4 short-syntax.

* **short_scalar_cast** [@Symfony]
                        Cast "(boolean)" and
                        "(integer)" should be written
                        as "(bool)" and "(int)".
                        "(double)" and "(real)" as
                        "(float)".

* **simplified_null_return** [@Symfony]
                        A return statement wishing to
                        return nothing should be
                        simply "return".

* **single_blank_line_at_eof** [@PSR2, @Symfony]
                        A file must always end with a
                        single empty line feed.

* **single_blank_line_before_namespace** [@Symfony]
                        There should be exactly one
                        blank line before a namespace
                        declaration.

* **single_import_per_statement** [@PSR2, @Symfony]
                        There MUST be one use keyword
                        per declaration.

* **single_line_after_imports** [@PSR2, @Symfony]
                        Each namespace use MUST go on
                        its own line and there MUST be
                        one blank line after the use
                        statements block.

* **single_quote** [@Symfony]
                        Convert double quotes to
                        single quotes for simple
                        strings.

* **space_after_semicolon** [@Symfony]
                        Fix whitespace after a
                        semicolon.

* **spaces_cast** [@Symfony]
                        A single space should be
                        between cast and variable.

* **standardize_not_equals** [@Symfony]
                        Replace all <> with !=.

* **strict**
                        Comparison should be strict.
                        (Risky fixer!)

* **strict_param**
                        Functions should be used with
                        $strict param. (Risky fixer!)

* **switch_case_semicolon_to_colon** [@PSR2, @Symfony]
                        A case should be followed by a
                        colon and not a semicolon.

* **switch_case_space** [@PSR2, @Symfony]
                        Removes extra spaces between
                        colon and case value.

* **ternary_operator_spaces** [@Symfony]
                        Standardize spaces around
                        ternary operator.

* **trailing_comma_in_multiline_array** [@Symfony]
                        PHP multi-line arrays should
                        have a trailing comma.

* **trim_array_spaces** [@Symfony]
                        Arrays should be formatted
                        like function/method
                        arguments, without leading or
                        trailing single line space.

* **unalign_double_arrow** [@Symfony]
                        Unalign double arrow symbols.

* **unalign_equals** [@Symfony]
                        Unalign equals symbols.

* **unary_operator_spaces** [@Symfony]
                        Unary operators should be
                        placed adjacent to their
                        operands.

* **unix_line_endings** [@PSR2, @Symfony]
                        All PHP files must use the
                        Unix LF line ending.

* **visibility_required** [@PSR2, @Symfony]
                        Visibility MUST be declared on
                        all properties and methods;
                        abstract and final MUST be
                        declared before the
                        visibility; static MUST be
                        declared after the visibility.

* **whitespace_after_comma_in_array** [@Symfony]
                        In array declaration, there
                        MUST be a whitespace after
                        each comma.

* **whitespacy_lines** [@Symfony]
                        Remove trailing whitespace at
                        the end of blank lines.


The ``--dry-run`` option displays the files that need to be
fixed but without actually modifying them:

.. code-block:: bash

    php php-cs-fixer.phar fix /path/to/code --dry-run

Instead of using command line options to customize the fixer, you can save the
project configuration in a ``.php_cs.dist`` file in the root directory
of your project. The file must return an instance of ``PhpCsFixer\ConfigInterface``,
which lets you configure the rules, the files and directories that
need to be analyzed. You may also create ``.php_cs`` file, which is
the local configuration that will be used instead of the project configuration. It
is a good practice to add that file into your ``.gitignore`` file.
With the ``--config`` option you can specify the path to the
``.php_cs`` file.

The example below will add two fixers to the default list of PSR2 set fixers:

.. code-block:: php

    <?php

    $finder = PhpCsFixer\Finder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return PhpCsFixer\Config::create()
        ->setRules(array(
            '@PSR2' => true,
            'strict_param' => true,
            'short_array_syntax' => true,
        ))
        ->finder($finder)
    ;

You may also use a blacklist for the Fixers instead of the above shown whitelist approach.
The following example shows how to use all ``Symfony`` Fixers but the ``full_opening_tag`` Fixer.

.. code-block:: php

    <?php

    $finder = PhpCsFixer\Finder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return PhpCsFixer\Config::create()
        ->setRules(array(
            '@Symfony' => true,
            'full_opening_tag' => false,
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

    return PhpCsFixer\Config::create()
        ->setUsingCache(false)
    ;

Cache file can be specified via ``--cache-file`` option or config file:

.. code-block:: php

    <?php

    return PhpCsFixer\Config::create()
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

Exit codes
----------

Exit code are build using following bit flags:

*  0 OK
*  4 Some files have invalid syntax (only in dry-run mode)
*  8 Some files need fixing (only in dry-run mode)
* 16 Configuration error of the application
* 32 Configuration error of a Fixer

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
