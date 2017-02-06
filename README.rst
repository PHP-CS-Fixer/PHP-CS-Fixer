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

    $ wget https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v2.0.0/php-cs-fixer.phar -O php-cs-fixer

or with curl:

.. code-block:: bash

    $ curl -L https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v2.0.0/php-cs-fixer.phar -o php-cs-fixer

then:

.. code-block:: bash

    $ sudo chmod a+x php-cs-fixer
    $ sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer

Then, just run ``php-cs-fixer``.

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

To install PHP CS Fixer, install Composer and issue the following command:

.. code-block:: bash

    $ ./composer.phar global require friendsofphp/php-cs-fixer

Then make sure you have ``~/.composer/vendor/bin`` in your ``PATH`` and
you're good to go:

.. code-block:: bash

    $ export PATH="$PATH:$HOME/.composer/vendor/bin"

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

    $ ./composer.phar global update friendsofphp/php-cs-fixer

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

    $ php php-cs-fixer.phar fix /path/to/dir
    $ php php-cs-fixer.phar fix /path/to/file

By default ``--path-mode`` is set to ``override``, which means, that if you specify the path to a file or a directory via 
command arguments, then the paths provided to a ``Finder`` in config file will be ignored. You can use ``--path-mode=intersection`` 
to merge paths from the config file and from the argument:

.. code-block:: bash

    $ php php-cs-fixer.phar fix --path-mode=intersection /path/to/dir

The ``--format`` option for the output format. Supported formats are ``txt`` (default one), ``json``, ``xml`` and ``junit``.

NOTE: When using ``junit`` format report generates in accordance with JUnit xml schema from Jenkins (see docs/junit-10.xsd).

The ``--verbose`` option will show the applied rules. When using the ``txt`` format it will also displays progress notifications.

The ``--rules`` option limits the rules to apply on the
project:

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/project --rules=@PSR2

By default, all PSR rules are run.

The ``--rules`` option lets you choose the exact rules to
apply (the rule names must be separated by a comma):

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/dir --rules=line_ending,full_opening_tag,indentation_type

You can also blacklist the rules you don't want by placing a dash in front of the rule name, if this is more convenient,
using ``-name_of_fixer``:

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/dir --rules=-full_opening_tag,-indentation_type

When using combinations of exact and blacklist rules, applying exact rules along with above blacklisted results:

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/project --rules=@Symfony,-@PSR1,-blank_line_before_return,strict_comparison

A combination of ``--dry-run`` and ``--diff`` will
display a summary of proposed fixes, leaving your files unchanged.

The ``--allow-risky`` option allows you to set whether risky rules may run. Default value is taken from config file.
Risky rule is a rule, which could change code behaviour. By default no risky rules are run.

The command can also read from standard input, in which case it won't
automatically fix anything:

.. code-block:: bash

    $ cat foo.php | php php-cs-fixer.phar fix --diff -

Choose from the list of available rules:

* **array_syntax**
   | PHP arrays should be declared using the configured syntax (requires PHP
   | >= 5.4 for short syntax).
   | *Rule is: configurable.*

* **binary_operator_spaces** [@Symfony]
   | Binary operators should be surrounded by at least one space.
   | *Rule is: configurable.*

* **blank_line_after_namespace** [@PSR2, @Symfony]
   | There MUST be one blank line after the namespace declaration.

* **blank_line_after_opening_tag** [@Symfony]
   | Ensure there is no code on the same line as the PHP open tag and it is
   | followed by a blank line.

* **blank_line_before_return** [@Symfony]
   | An empty line feed should precede a return statement.

* **braces** [@PSR2, @Symfony]
   | The body of each structure MUST be enclosed by braces. Braces should be
   | properly placed. Body of braces should be properly indented.
   | *Rule is: configurable.*

* **cast_spaces** [@Symfony]
   | A single space should be between cast and variable.

* **class_definition** [@PSR2, @Symfony]
   | Whitespace around the keywords of a class, trait or interfaces
   | definition should be one space.
   | *Rule is: configurable.*

* **class_keyword_remove**
   | Converts ``::class`` keywords to FQCN strings. Requires PHP >= 5.5.

* **combine_consecutive_unsets**
   | Calling ``unset`` on multiple items should be done in one call.

* **concat_space** [@Symfony]
   | Concatenation should be spaced according configuration.
   | *Rule is: configurable.*

* **declare_equal_normalize** [@Symfony]
   | Equal sign in declare statement should not be surrounded by spaces.

* **declare_strict_types**
   | Force strict types declaration in all files. Requires PHP >= 7.0.
   | *Rule is: risky.*

* **dir_constant**
   | Replaces ``dirname(__FILE__)`` expression with equivalent ``__DIR__``
   | constant.
   | *Rule is: risky.*

* **elseif** [@PSR2, @Symfony]
   | The keyword ``elseif`` should be used instead of ``else if`` so that all
   | control keywords look like single words.

* **encoding** [@PSR1, @PSR2, @Symfony]
   | PHP code MUST use only UTF-8 without BOM (remove BOM).

* **ereg_to_preg**
   | Replace deprecated ``ereg`` regular expression functions with preg.
   | *Rule is: risky.*

* **full_opening_tag** [@PSR1, @PSR2, @Symfony]
   | PHP code must use the long ``<?php`` tags or short-echo ``<?=`` tags and not
   | other tag variations.

* **function_declaration** [@PSR2, @Symfony]
   | Spaces should be properly placed in a function declaration.

* **function_typehint_space** [@Symfony]
   | Add missing space between function's argument and its typehint.

* **general_phpdoc_annotation_remove**
   | Configured annotations should be omitted from phpdocs.
   | *Rule is: configurable.*

* **hash_to_slash_comment** [@Symfony]
   | Single line comments should use double slashes ``//`` and not hash ``#``.

* **header_comment**
   | Add, replace or remove header comment.
   | *Rule is: configurable.*

* **heredoc_to_nowdoc**
   | Convert ``heredoc`` to ``nowdoc`` where possible.

* **include** [@Symfony]
   | Include/Require and file path should be divided with a single space.
   | File path should not be placed under brackets.

* **indentation_type** [@PSR2, @Symfony]
   | Code MUST use configured indentation type.

* **is_null** [@Symfony:risky]
   | Replaces is_null(parameter) expression with ``null === parameter``.
   | *Rule is: configurable, risky.*

* **line_ending** [@PSR2, @Symfony]
   | All PHP files must use same line ending.

* **linebreak_after_opening_tag**
   | Ensure there is no code on the same line as the PHP open tag.

* **lowercase_cast** [@Symfony]
   | Cast should be written in lower case.

* **lowercase_constants** [@PSR2, @Symfony]
   | The PHP constants ``true``, ``false``, and ``null`` MUST be in lower case.

* **lowercase_keywords** [@PSR2, @Symfony]
   | PHP keywords MUST be in lower case.

* **mb_str_functions**
   | Replace non multibyte-safe functions with corresponding mb function.
   | *Rule is: risky.*

* **method_argument_space** [@PSR2, @Symfony]
   | In method arguments and method call, there MUST NOT be a space before
   | each comma and there MUST be one space after each comma.

* **method_separation** [@Symfony]
   | Methods must be separated with one blank line.

* **modernize_types_casting**
   | Replaces ``intval``, ``floatval``, ``doubleval``, ``strval`` and ``boolval``
   | function calls with according type casting operator.
   | *Rule is: risky.*

* **native_function_casing** [@Symfony]
   | Function defined by PHP should be called using the correct casing.

* **native_function_invocation**
   | Add leading ``\`` before function invocation of internal function to speed
   | up resolving.
   | *Rule is: configurable, risky.*

* **new_with_braces** [@Symfony]
   | All instances created with new keyword must be followed by braces.

* **no_alias_functions** [@Symfony:risky]
   | Master functions shall be used instead of aliases.
   | *Rule is: risky.*

* **no_blank_lines_after_class_opening** [@Symfony]
   | There should be no empty lines after class opening brace.

* **no_blank_lines_after_phpdoc** [@Symfony]
   | There should not be blank lines between docblock and the documented
   | element.

* **no_blank_lines_before_namespace**
   | There should be no blank lines before a namespace declaration.

* **no_closing_tag** [@PSR2, @Symfony]
   | The closing ``?>`` tag MUST be omitted from files containing only PHP.

* **no_empty_comment** [@Symfony]
   | There should not be any empty comments.

* **no_empty_phpdoc** [@Symfony]
   | There should not be empty PHPDoc blocks.

* **no_empty_statement** [@Symfony]
   | Remove useless semicolon statements.

* **no_extra_consecutive_blank_lines** [@Symfony]
   | Removes extra blank lines and/or blank lines following configuration.
   | *Rule is: configurable.*

* **no_leading_import_slash** [@Symfony]
   | Remove leading slashes in use clauses.

* **no_leading_namespace_whitespace** [@Symfony]
   | The namespace declaration line shouldn't contain leading whitespace.

* **no_mixed_echo_print** [@Symfony]
   | Either language construct ``print`` or ``echo`` should be used.
   | *Rule is: configurable.*

* **no_multiline_whitespace_around_double_arrow** [@Symfony]
   | Operator ``=>`` should not be surrounded by multi-line whitespaces.

* **no_multiline_whitespace_before_semicolons**
   | Multi-line whitespace before closing semicolon are prohibited.

* **no_php4_constructor**
   | Convert PHP4-style constructors to ``__construct``.
   | *Rule is: risky.*

* **no_short_bool_cast** [@Symfony]
   | Short cast ``bool`` using double exclamation mark should not be used.

* **no_short_echo_tag**
   | Replace short-echo ``<?=`` with long format ``<?php echo`` syntax.

* **no_singleline_whitespace_before_semicolons** [@Symfony]
   | Single-line whitespace before closing semicolon are prohibited.

* **no_spaces_after_function_name** [@PSR2, @Symfony]
   | When making a method or function call, there MUST NOT be a space between
   | the method or function name and the opening parenthesis.

* **no_spaces_around_offset** [@Symfony]
   | There MUST NOT be spaces around offset braces.
   | *Rule is: configurable.*

* **no_spaces_inside_parenthesis** [@PSR2, @Symfony]
   | There MUST NOT be a space after the opening parenthesis. There MUST NOT
   | be a space before the closing parenthesis.

* **no_trailing_comma_in_list_call** [@Symfony]
   | Remove trailing commas in list function calls.

* **no_trailing_comma_in_singleline_array** [@Symfony]
   | PHP single-line arrays should not have trailing comma.

* **no_trailing_whitespace** [@PSR2, @Symfony]
   | Remove trailing whitespace at the end of non-blank lines.

* **no_trailing_whitespace_in_comment** [@PSR2, @Symfony]
   | There MUST be no trailing spaces inside comments and phpdocs.

* **no_unneeded_control_parentheses** [@Symfony]
   | Removes unneeded parentheses around control statements.
   | *Rule is: configurable.*

* **no_unreachable_default_argument_value**
   | In method arguments there must not be arguments with default values
   | before non-default ones.

* **no_unused_imports** [@Symfony]
   | Unused use statements must be removed.

* **no_useless_else**
   | There should not be useless ``else`` cases.

* **no_useless_return**
   | There should not be an empty return statement at the end of a function.

* **no_whitespace_before_comma_in_array** [@Symfony]
   | In array declaration, there MUST NOT be a whitespace before each comma.

* **no_whitespace_in_blank_line** [@Symfony]
   | Remove trailing whitespace at the end of blank lines.

* **normalize_index_brace** [@Symfony]
   | Array index should always be written by using square braces.

* **not_operator_with_space**
   | Logical NOT operators (``!``) should have leading and trailing
   | whitespaces.

* **not_operator_with_successor_space**
   | Logical NOT operators (``!``) should have one trailing whitespace.

* **object_operator_without_whitespace** [@Symfony]
   | There should not be space before or after object ``T_OBJECT_OPERATOR``
   | ``->``.

* **ordered_class_elements**
   | Orders the elements of classes/interfaces/traits.
   | *Rule is: configurable.*

* **ordered_imports**
   | Ordering use statements.
   | *Rule is: configurable.*

* **php_unit_construct** [@Symfony:risky]
   | PHPUnit assertion method calls like "->assertSame(true, $foo)" should be
   | written with dedicated method like "->assertTrue($foo)".
   | *Rule is: configurable, risky.*

* **php_unit_dedicate_assert** [@Symfony:risky]
   | PHPUnit assertions like "assertInternalType", "assertFileExists", should
   | be used over "assertTrue".
   | *Rule is: configurable, risky.*

* **php_unit_fqcn_annotation** [@Symfony]
   | PHPUnit annotations should be a FQCNs including a root namespace.

* **php_unit_strict**
   | PHPUnit methods like ``assertSame`` should be used instead of
   | ``assertEquals``.
   | *Rule is: configurable, risky.*

* **phpdoc_add_missing_param_annotation**
   | Phpdoc should contain @param for all params.
   | *Rule is: configurable.*

* **phpdoc_align** [@Symfony]
   | All items of the @param, @throws, @return, @var, and @type phpdoc tags
   | must be aligned vertically.

* **phpdoc_annotation_without_dot** [@Symfony]
   | Phpdocs annotation descriptions should not be a sentence.

* **phpdoc_indent** [@Symfony]
   | Docblocks should have the same indentation as the documented subject.

* **phpdoc_inline_tag** [@Symfony]
   | Fix phpdoc inline tags, make inheritdoc always inline.

* **phpdoc_no_access** [@Symfony]
   | @access annotations should be omitted from phpdocs.

* **phpdoc_no_alias_tag** [@Symfony]
   | No alias PHPDoc tags should be used.
   | *Rule is: configurable.*

* **phpdoc_no_empty_return** [@Symfony]
   | @return void and @return null annotations should be omitted from
   | phpdocs.

* **phpdoc_no_package** [@Symfony]
   | @package and @subpackage annotations should be omitted from phpdocs.

* **phpdoc_no_useless_inheritdoc** [@Symfony]
   | Classy that does not inherit must not have inheritdoc tags.

* **phpdoc_order**
   | Annotations in phpdocs should be ordered so that param annotations come
   | first, then throws annotations, then return annotations.

* **phpdoc_return_self_reference** [@Symfony]
   | The type of ``@return`` annotations of methods returning a reference to
   | itself must the configured one.
   | *Rule is: configurable.*

* **phpdoc_scalar** [@Symfony]
   | Scalar types should always be written in the same form. ``int`` not
   | ``integer``, ``bool`` not ``boolean``, ``float`` not ``real`` or ``double``.

* **phpdoc_separation** [@Symfony]
   | Annotations in phpdocs should be grouped together so that annotations of
   | the same type immediately follow each other, and annotations of a
   | different type are separated by a single blank line.

* **phpdoc_single_line_var_spacing** [@Symfony]
   | Single line @var PHPDoc should have proper spacing.

* **phpdoc_summary** [@Symfony]
   | Phpdocs summary should end in either a full stop, exclamation mark, or
   | question mark.

* **phpdoc_to_comment** [@Symfony]
   | Docblocks should only be used on structural elements.

* **phpdoc_trim** [@Symfony]
   | Phpdocs should start and end with content, excluding the very first and
   | last line of the docblocks.

* **phpdoc_types** [@Symfony]
   | The correct case must be used for standard PHP types in phpdoc.

* **phpdoc_var_without_name** [@Symfony]
   | @var and @type annotations should not contain the variable name.

* **pow_to_exponentiation** [@PHP56Migration, @PHP70Migration, @PHP71Migration]
   | Converts ``pow()`` to the ``**`` operator. Requires PHP >= 5.6.
   | *Rule is: risky.*

* **pre_increment** [@Symfony]
   | Pre incrementation/decrementation should be used if possible.

* **protected_to_private**
   | Converts ``protected`` variables and methods to ``private`` where possible.

* **psr0**
   | Classes must be in a path that matches their namespace, be at least one
   | namespace deep and the class name should match the file name.
   | *Rule is: configurable, risky.*

* **psr4**
   | Class names should match the file name.
   | *Rule is: risky.*

* **random_api_migration** [@PHP70Migration, @PHP71Migration]
   | Replaces ``rand``, ``mt_rand``, ``srand``, ``getrandmax`` functions calls with
   | their ``mt_*`` analogs.
   | *Rule is: configurable, risky.*

* **return_type_declaration** [@Symfony]
   | There should be one or no space before colon, and one space after it in
   | return type declarations, according to configuration.
   | *Rule is: configurable.*

* **self_accessor** [@Symfony]
   | Inside a classy element "self" should be preferred to the class name
   | itself.

* **semicolon_after_instruction**
   | Instructions must be terminated with a semicolon.

* **short_scalar_cast** [@Symfony]
   | Cast ``(boolean)`` and ``(integer)`` should be written as ``(bool)`` and
   | ``(int)``, ``(double)`` and ``(real)`` as ``(float)``.

* **silenced_deprecation_error** [@Symfony:risky]
   | Ensures deprecation notices are silenced.
   | *Rule is: risky.*

* **simplified_null_return**
   | A return statement wishing to return ``void`` should not return ``null``.
   | *Rule is: risky.*

* **single_blank_line_at_eof** [@PSR2, @Symfony]
   | A PHP file without end tag must always end with a single empty line
   | feed.

* **single_blank_line_before_namespace** [@Symfony]
   | There should be exactly one blank line before a namespace declaration.

* **single_class_element_per_statement** [@PSR2, @Symfony]
   | There MUST NOT be more than one property or constant declared per
   | statement.
   | *Rule is: configurable.*

* **single_import_per_statement** [@PSR2, @Symfony]
   | There MUST be one use keyword per declaration.

* **single_line_after_imports** [@PSR2, @Symfony]
   | Each namespace use MUST go on its own line and there MUST be one blank
   | line after the use statements block.

* **single_quote** [@Symfony]
   | Convert double quotes to single quotes for simple strings.

* **space_after_semicolon** [@Symfony]
   | Fix whitespace after a semicolon.

* **standardize_not_equals** [@Symfony]
   | Replace all ``<>`` with ``!=``.

* **strict_comparison**
   | Comparisons should be strict.
   | *Rule is: risky.*

* **strict_param**
   | Functions should be used with ``$strict`` param set to ``true``.
   | *Rule is: risky.*

* **switch_case_semicolon_to_colon** [@PSR2, @Symfony]
   | A case should be followed by a colon and not a semicolon.

* **switch_case_space** [@PSR2, @Symfony]
   | Removes extra spaces between colon and case value.

* **ternary_operator_spaces** [@Symfony]
   | Standardize spaces around ternary operator.

* **ternary_to_null_coalescing**
   | Use ``null`` coalescing operator ``??`` where possible.

* **trailing_comma_in_multiline_array** [@Symfony]
   | PHP multi-line arrays should have a trailing comma.

* **trim_array_spaces** [@Symfony]
   | Arrays should be formatted like function/method arguments, without
   | leading or trailing single line space.

* **unary_operator_spaces** [@Symfony]
   | Unary operators should be placed adjacent to their operands.

* **visibility_required** [@PSR2, @Symfony, @PHP71Migration]
   | Visibility MUST be declared on all properties and methods; abstract and
   | final MUST be declared before the visibility; static MUST be declared
   | after the visibility.
   | *Rule is: configurable.*

* **whitespace_after_comma_in_array** [@Symfony]
   | In array declaration, there MUST be a whitespace after each comma.


The ``--dry-run`` option displays the files that need to be
fixed but without actually modifying them:

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/code --dry-run

Instead of using command line options to customize the rule, you can save the
project configuration in a ``.php_cs.dist`` file in the root directory
of your project. The file must return an instance of ``PhpCsFixer\ConfigInterface``,
which lets you configure the rules, the files and directories that
need to be analyzed. You may also create ``.php_cs`` file, which is
the local configuration that will be used instead of the project configuration. It
is a good practice to add that file into your ``.gitignore`` file.
With the ``--config`` option you can specify the path to the
``.php_cs`` file.

The example below will add two rules to the default list of PSR2 set rules:

.. code-block:: php

    <?php

    $finder = PhpCsFixer\Finder::create()
        ->exclude('somedir')
        ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
        ->in(__DIR__)
    ;

    return PhpCsFixer\Config::create()
        ->setRules(array(
            '@PSR2' => true,
            'strict_param' => true,
            'array_syntax' => array('syntax' => 'short'),
        ))
        ->setFinder($finder)
    ;

**NOTE**: ``exclude`` will work only for directories, so if you need to exclude file, try ``notPath``.

See `Symfony\\Finder <http://symfony.com/doc/current/components/finder.html>`_
online documentation for other `Finder` methods.

You may also use a blacklist for the rules instead of the above shown whitelist approach.
The following example shows how to use all ``Symfony`` rules but the ``full_opening_tag`` rule.

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
        ->setFinder($finder)
    ;

You may want to use non-linux whitespaces in your project. Then you need to
configure them in your config file. Please be aware that this feature is
experimental.

.. code-block:: php

    <?php

    return PhpCsFixer\Config::create()
        ->setIndent("\t")
        ->setLineEnding("\r\n")
    ;

By using ``--using-cache`` option with yes or no you can set if the caching
mechanism should be used.

Caching
-------

The caching mechanism is enabled by default. This will speed up further runs by
fixing only files that were modified since the last run. The tool will fix all
files if the tool version has changed or the list of rules has changed.
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

Using PHP CS Fixer on CI
------------------------

Require ``friendsofphp/php-cs-fixer`` as a ``dev`` dependency:

.. code-block:: bash

    $ ./composer.phar require --dev friendsofphp/php-cs-fixer

Then, add the following command to your CI:

.. code-block:: bash

    $ vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run --using-cache=no --path-mode=intersection `git diff --name-only --diff-filter=ACMRTUXB $COMMIT_RANGE`

Where ``$COMMIT_RANGE`` is your range of commits, eg ``$TRAVIS_COMMIT_RANGE`` or ``HEAD~..HEAD``.

Exit codes
----------

Exit code is build using following bit flags:

*  0 OK.
*  1 General error (or PHP/HHVM minimal requirement not matched).
*  4 Some files have invalid syntax (only in dry-run mode).
*  8 Some files need fixing (only in dry-run mode).
* 16 Configuration error of the application.
* 32 Configuration error of a Fixer.
* 64 Exception raised within the application.

(applies to exit codes of the `fix` command only)

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

The tool comes with quite a few built-in fixers, but everyone is more than
welcome to `contribute`_ more of them.

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

.. _php-cs-fixer.phar: https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v2.0.0/php-cs-fixer.phar
.. _Atom:              https://github.com/Glavin001/atom-beautify
.. _NetBeans:          http://plugins.netbeans.org/plugin/49042/php-cs-fixer
.. _PhpStorm:          http://tzfrs.de/2015/01/automatically-format-code-to-match-psr-standards-with-phpstorm
.. _Sublime Text:      https://github.com/benmatselby/sublime-phpcs
.. _Vim:               https://github.com/stephpy/vim-php-cs-fixer
.. _contribute:        https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/CONTRIBUTING.md
