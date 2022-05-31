========================================
Rule ``whitespace_before_statement_end``
========================================

Forbid multi-line whitespace before a statement end (comma or semicolon) or
moves it to the next line for multiline statements.

Configuration
-------------

``semicolon_strategy``
~~~~~~~~~~~~~~~~~~~~~~

Strategy to apply to semicolon.

Allowed values: ``'new_line_for_multiline_statement'``, ``'no_whitespace'``, ``'none'``

Default value: ``'new_line_for_multiline_statement'``

``comma_strategy``
~~~~~~~~~~~~~~~~~~

Strategy to apply to comma.

Allowed values: ``'new_line_for_multiline_statement'``, ``'no_whitespace'``, ``'none'``

Default value: ``'new_line_for_multiline_statement'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $bar = [
        $foo
            ->bar()
   -        ->baz(),
   +        ->baz()
   +    ,
    ];

    return $bar
        ->bar()
   -    ->baz();
   +    ->baz()
   +;

Example #2
~~~~~~~~~~

With configuration: ``['semicolon_strategy' => 'no_whitespace']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    return $foo
        ->bar()
   -    ->baz()  ;
   +    ->baz();

Example #3
~~~~~~~~~~

With configuration: ``['comma_strategy' => 'no_whitespace']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    return [
        $foo
            ->bar()
   -        ->baz()  ,
   +        ->baz(),
    ];

Rule sets
---------

The rule is part of the following rule sets:

@PHP73Migration
  Using the `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ rule set will enable the ``whitespace_before_statement_end`` rule with the config below:

  ``['comma_strategy' => 'no_whitespace', 'semicolon_strategy' => 'no_whitespace']``

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``whitespace_before_statement_end`` rule with the config below:

  ``['comma_strategy' => 'no_whitespace', 'semicolon_strategy' => 'no_whitespace']``

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``whitespace_before_statement_end`` rule with the config below:

  ``['comma_strategy' => 'no_whitespace', 'semicolon_strategy' => 'no_whitespace']``

@PHP81Migration
  Using the `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ rule set will enable the ``whitespace_before_statement_end`` rule with the config below:

  ``['comma_strategy' => 'no_whitespace', 'semicolon_strategy' => 'no_whitespace']``

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``whitespace_before_statement_end`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``whitespace_before_statement_end`` rule with the config below:

  ``['comma_strategy' => 'no_whitespace', 'semicolon_strategy' => 'no_whitespace']``
