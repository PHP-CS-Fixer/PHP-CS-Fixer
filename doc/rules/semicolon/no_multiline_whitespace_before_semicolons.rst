==================================================
Rule ``no_multiline_whitespace_before_semicolons``
==================================================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``multiline_whitespace_before_semicolons`` instead.

Multi-line whitespace before closing semicolon are prohibited.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function foo () {
   -    return 1 + 2
   -        ;
   +    return 1 + 2;
    }
