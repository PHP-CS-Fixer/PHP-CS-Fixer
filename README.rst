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

You can also blacklist the fixers you don't want by placing a dash in front of the fixer name, if this is more convenient,
using ``-name_of_fixer``:

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

* **psr0** [PSR-0]
                        Classes must be in a path that
                        matches their namespace, be at
                        least one namespace deep, and
                        the class name should match
                        the file name.

* **encoding** [PSR-1]
                        PHP code MUST use only UTF-8
                        without BOM (remove BOM).

* **short_tag** [PSR-1]
                        PHP code must use the long
                        <?php ?> tags or the
                        short-echo <?= ?> tags; it
                        must not use the other tag
                        variations.

* **braces** [PSR-2]
                        The body of each structure
                        MUST be enclosed by braces.
                        Braces should be properly
                        placed. Body of braces should
                        be properly indented.

* **class_definition** [PSR-2]
                        Whitespace around the key
                        words of a class, trait or
                        interfaces definition should
                        be one space.

* **elseif** [PSR-2]
                        The keyword elseif should be
                        used instead of else if so
                        that all control keywords
                        looks like single words.

* **eof_ending** [PSR-2]
                        A file must always end with a
                        single empty line feed.

* **function_call_space** [PSR-2]
                        When making a method or
                        function call, there MUST NOT
                        be a space between the method
                        or function name and the
                        opening parenthesis.

* **function_declaration** [PSR-2]
                        Spaces should be properly
                        placed in a function
                        declaration.

* **indentation** [PSR-2]
                        Code MUST use an indent of 4
                        spaces, and MUST NOT use tabs
                        for indenting.

* **line_after_namespace** [PSR-2]
                        There MUST be one blank line
                        after the namespace
                        declaration.

* **linefeed** [PSR-2]
                        All PHP files must use the
                        Unix LF (linefeed) line
                        ending.

* **lowercase_constants** [PSR-2]
                        The PHP constants true, false,
                        and null MUST be in lower
                        case.

* **lowercase_keywords** [PSR-2]
                        PHP keywords MUST be in lower
                        case.

* **method_argument_space** [PSR-2]
                        In method arguments and method
                        call, there MUST NOT be a
                        space before each comma and
                        there MUST be one space after
                        each comma.

* **multiple_use** [PSR-2]
                        There MUST be one use keyword
                        per declaration.

* **parenthesis** [PSR-2]
                        There MUST NOT be a space
                        after the opening parenthesis.
                        There MUST NOT be a space
                        before the closing
                        parenthesis.

* **php_closing_tag** [PSR-2]
                        The closing ?> tag MUST be
                        omitted from files containing
                        only PHP.

* **single_line_after_imports** [PSR-2]
                        Each namespace use MUST go on
                        its own line and there MUST be
                        one blank line after the use
                        statements block.

* **switch_case_space** [PSR-2]
                        Removes extra spaces between
                        colon and case value.

* **trailing_spaces** [PSR-2]
                        Remove trailing whitespace at
                        the end of non-blank lines.

* **visibility** [PSR-2]
                        Visibility MUST be declared on
                        all properties and methods;
                        abstract and final MUST be
                        declared before the
                        visibility; static MUST be
                        declared after the visibility.

* **array_element_no_space_before_comma** [symfony]
                        In array declaration, there
                        MUST NOT be a whitespace
                        before each comma.

* **array_element_white_space_after_comma** [symfony]
                        In array declaration, there
                        MUST be a whitespace after
                        each comma.

* **blankline_after_open_tag** [symfony]
                        Ensure there is no code on the
                        same line as the PHP open tag
                        and it is followed by a
                        blankline.

* **concat_without_spaces** [symfony]
                        Concatenation should be used
                        without spaces.

* **double_arrow_multiline_whitespaces** [symfony]
                        Operator => should not be
                        surrounded by multi-line
                        whitespaces.

* **duplicate_semicolon** [symfony]
                        Remove duplicated semicolons.

* **empty_return** [symfony]
                        A return statement wishing to
                        return nothing should be
                        simply "return".

* **extra_empty_lines** [symfony]
                        Removes extra empty lines.

* **function_typehint_space** [symfony]
                        Add missing space between
                        function's argument and its
                        typehint.

* **hash_to_slash_comment** [symfony]
                        Single line comments should
                        use double slashes (//) and
                        not hash (#).

* **include** [symfony]
                        Include/Require and file path
                        should be divided with a
                        single space. File path should
                        not be placed under brackets.

* **join_function** [symfony]
                        Implode function should be
                        used instead of join function.

* **list_commas** [symfony]
                        Remove trailing commas in list
                        function calls.

* **method_argument_default_value** [symfony]
                        In method arguments there must
                        not be arguments with default
                        values before non-default
                        ones.

* **multiline_array_trailing_comma** [symfony]
                        PHP multi-line arrays should
                        have a trailing comma.

* **namespace_no_leading_whitespace** [symfony]
                        The namespace declaration line
                        shouldn't contain leading
                        whitespace.

* **new_with_braces** [symfony]
                        All instances created with new
                        keyword must be followed by
                        braces.

* **no_blank_lines_after_class_opening** [symfony]
                        There should be no empty lines
                        after class opening brace.

* **no_empty_lines_after_phpdocs** [symfony]
                        There should not be blank
                        lines between docblock and the
                        documented element.

* **object_operator** [symfony]
                        There should not be space
                        before or after object
                        T_OBJECT_OPERATOR.

* **operators_spaces** [symfony]
                        Binary operators should be
                        surrounded by at least one
                        space.

* **phpdoc_indent** [symfony]
                        Docblocks should have the same
                        indentation as the documented
                        subject.

* **phpdoc_inline_tag** [symfony]
                        Fix PHPDoc inline tags, make
                        inheritdoc always inline.

* **phpdoc_no_access** [symfony]
                        @access annotations should be
                        omitted from phpdocs.

* **phpdoc_no_empty_return** [symfony]
                        @return void and @return null
                        annotations should be omitted
                        from phpdocs.

* **phpdoc_no_package** [symfony]
                        @package and @subpackage
                        annotations should be omitted
                        from phpdocs.

* **phpdoc_params** [symfony]
                        All items of the @param,
                        @throws, @return, @var, and
                        @type phpdoc tags must be
                        aligned vertically.

* **phpdoc_scalar** [symfony]
                        Scalar types should always be
                        written in the same form.
                        "int", not "integer"; "bool",
                        not "boolean"; "float", not
                        "real" or "double".

* **phpdoc_separation** [symfony]
                        Annotations in phpdocs should
                        be grouped together so that
                        annotations of the same type
                        immediately follow each other,
                        and annotations of a different
                        type are separated by a single
                        blank line.

* **phpdoc_short_description** [symfony]
                        Phpdocs short descriptions
                        should end in either a full
                        stop, exclamation mark, or
                        question mark.

* **phpdoc_to_comment** [symfony]
                        Docblocks should only be used
                        on structural elements.

* **phpdoc_trim** [symfony]
                        Phpdocs should start and end
                        with content, excluding the
                        very first and last line of
                        the docblocks.

* **phpdoc_type_to_var** [symfony]
                        @type should always be written
                        as @var.

* **phpdoc_types** [symfony]
                        The correct case must be used
                        for standard PHP types in
                        phpdoc.

* **phpdoc_var_without_name** [symfony]
                        @var and @type annotations
                        should not contain the
                        variable name.

* **pre_increment** [symfony]
                        Pre
                        incrementation/decrementation
                        should be used if possible.

* **print_to_echo** [symfony]
                        Converts print language
                        construct to echo if possible.

* **remove_leading_slash_use** [symfony]
                        Remove leading slashes in use
                        clauses.

* **remove_lines_between_uses** [symfony]
                        Removes line breaks between
                        use statements.

* **return** [symfony]
                        An empty line feed should
                        precede a return statement.

* **self_accessor** [symfony]
                        Inside a classy element "self"
                        should be preferred to the
                        class name itself.

* **short_bool_cast** [symfony]
                        Short cast bool using double
                        exclamation mark should not be
                        used.

* **single_array_no_trailing_comma** [symfony]
                        PHP single-line arrays should
                        not have trailing comma.

* **single_blank_line_before_namespace** [symfony]
                        There should be exactly one
                        blank line before a namespace
                        declaration.

* **single_quote** [symfony]
                        Convert double quotes to
                        single quotes for simple
                        strings.

* **spaces_after_semicolon** [symfony]
                        Fix whitespace after a
                        semicolon.

* **spaces_before_semicolon** [symfony]
                        Single-line whitespace before
                        closing semicolon are
                        prohibited.

* **spaces_cast** [symfony]
                        A single space should be
                        between cast and variable.

* **standardize_not_equal** [symfony]
                        Replace all <> with !=.

* **ternary_spaces** [symfony]
                        Standardize spaces around
                        ternary operator.

* **trim_array_spaces** [symfony]
                        Arrays should be formatted
                        like function/method
                        arguments, without leading or
                        trailing single line space.

* **unalign_double_arrow** [symfony]
                        Unalign double arrow symbols.

* **unalign_equals** [symfony]
                        Unalign equals symbols.

* **unary_operators_spaces** [symfony]
                        Unary operators should be
                        placed adjacent to their
                        operands.

* **unneeded_control_parentheses** [symfony]
                        Removes unneeded parentheses
                        around control statements.

* **unused_use** [symfony]
                        Unused use statements must be
                        removed.

* **whitespacy_lines** [symfony]
                        Remove trailing whitespace at
                        the end of blank lines.

* **align_double_arrow** [contrib]
                        Align double arrow symbols in
                        consecutive lines.

* **align_equals** [contrib]
                        Align equals symbols in
                        consecutive lines.

* **concat_with_spaces** [contrib]
                        Concatenation should be used
                        with at least one whitespace
                        around.

* **echo_to_print** [contrib]
                        Converts echo language
                        construct to print if
                        possible.

* **ereg_to_preg** [contrib]
                        Replace deprecated ereg
                        regular expression functions
                        with preg. Warning! This could
                        change code behavior.

* **header_comment** [contrib]
                        Add, replace or remove header
                        comment.

* **logical_not_operators_with_spaces** [contrib]
                        Logical NOT operators (!)
                        should have leading and
                        trailing whitespaces.

* **logical_not_operators_with_successor_space** [contrib]
                        Logical NOT operators (!)
                        should have one trailing
                        whitespace.

* **long_array_syntax** [contrib]
                        Arrays should use the long
                        syntax.

* **multiline_spaces_before_semicolon** [contrib]
                        Multi-line whitespace before
                        closing semicolon are
                        prohibited.

* **native_function_casing** [contrib]
                        Function defined by PHP should
                        be called using the correct
                        casing.

* **newline_after_open_tag** [contrib]
                        Ensure there is no code on the
                        same line as the PHP open tag.

* **no_blank_lines_before_namespace** [contrib]
                        There should be no blank lines
                        before a namespace
                        declaration.

* **ordered_use** [contrib]
                        Ordering use statements.

* **php4_constructor** [contrib]
                        Convert PHP4-style
                        constructors to __construct.
                        Warning! This could change
                        code behavior.

* **php_unit_construct** [contrib]
                        PHPUnit assertion method calls
                        like "->assertSame(true,
                        $foo)" should be written with
                        dedicated method like
                        "->assertTrue($foo)". Warning!
                        This could change code
                        behavior.

* **php_unit_strict** [contrib]
                        PHPUnit methods like
                        "assertSame" should be used
                        instead of "assertEquals".
                        Warning! This could change
                        code behavior.

* **phpdoc_order** [contrib]
                        Annotations in phpdocs should
                        be ordered so that param
                        annotations come first, then
                        throws annotations, then
                        return annotations.

* **phpdoc_var_to_type** [contrib]
                        @var should always be written
                        as @type.

* **short_array_syntax** [contrib]
                        PHP arrays should use the PHP
                        5.4 short-syntax.

* **short_echo_tag** [contrib]
                        Replace short-echo <?= with
                        long format <?php echo syntax.

* **strict** [contrib]
                        Comparison should be strict.
                        Warning! This could change
                        code behavior.

* **strict_param** [contrib]
                        Functions should be used with
                        $strict param. Warning! This
                        could change code behavior.


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
``Symfony\CS\ConfigInterface``, which lets you configure the fixers, the level, the files,
and directories that need to be analyzed. The example below will add two contrib fixers
to the default list of symfony-level fixers:

.. code-block:: php

    <?php

    $finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->fixers(array('strict_param', 'short_array_syntax'))
        ->finder($finder)
    ;

If you want complete control over which fixers you use, you may use the empty level and
then specify all fixers to be used:

.. code-block:: php

    <?php

    $finder = Symfony\CS\Finder\DefaultFinder::create()
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
        ->fixers(array('trailing_spaces', 'encoding'))
        ->finder($finder)
    ;

You may also use a blacklist for the Fixers instead of the above shown whitelist approach.
The following example shows how to use all ``symfony`` Fixers but the ``psr0`` fixer.
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

Caching
-------

You can enable caching by returning a custom config with caching enabled. This will
speed up further runs.

.. code-block:: php

    <?php

    return Symfony\CS\Config\Config::create()
        ->setUsingCache(true)
    ;

Exit codes
----------
*  0 OK
*  1 No changes made
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

A *config* knows about the CS level and the files and directories that must be
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
