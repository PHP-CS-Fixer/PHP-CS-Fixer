===============================================
Rule ``multiline_whitespace_before_semicolons``
===============================================

Forbid multi-line whitespace before the closing semicolon or move the semicolon
to the new line for chained calls.

Warning
-------

This rule is deprecated and will be removed on next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``whitespace_before_statement_end`` instead.

Configuration
-------------

``strategy``
~~~~~~~~~~~~

Forbid multi-line whitespace or move the semicolon to the new line for chained
calls.

Allowed values: ``'new_line_for_chained_calls'``, ``'no_multi_line'``

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
    function foo () {
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
                            $this->method1()
                                ->method2()
   -                            ->method(3);
   +                            ->method(3)
   +;
                        ?>
