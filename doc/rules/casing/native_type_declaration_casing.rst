=======================================
Rule ``native_type_declaration_casing``
=======================================

Native type hints for constants should use the correct case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo
    {
   -    const INT BAR = 1;
   +    const int BAR = 1;
    }
