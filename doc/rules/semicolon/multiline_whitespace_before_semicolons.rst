===============================================
Rule ``multiline_whitespace_before_semicolons``
===============================================

Forbid multi-line whitespace before the closing semicolon or move the semicolon
to the new line for chained calls.

Configuration
-------------

``strategy``
~~~~~~~~~~~~

Forbid multi-line whitespace or move the semicolon to the new line for chained
calls.

Allowed values: ``'new_line_for_chained_calls'`` and ``'no_multi_line'``

Default value: ``'no_multi_line'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function foo() {
   -    return 1 + 2
   -        ;
   +    return 1 + 2;
    }

Example #2
~~~~~~~~~~

With configuration: ``['strategy' => 'new_line_for_chained_calls']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $object->method1()
        ->method2()
   -    ->method(3);
   +    ->method(3)
   +;

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['strategy' => 'new_line_for_chained_calls']``


Source class
------------

`PhpCsFixer\\Fixer\\Semicolon\\MultilineWhitespaceBeforeSemicolonsFixer <./../src/Fixer/Semicolon/MultilineWhitespaceBeforeSemicolonsFixer.php>`_
