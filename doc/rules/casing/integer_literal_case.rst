=============================
Rule ``integer_literal_case``
=============================

Integer literals must be in correct case.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``numeric_literal_case`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = 0Xff;
   -$bar = 0B11111111;
   +$foo = 0xFF;
   +$bar = 0b11111111;
