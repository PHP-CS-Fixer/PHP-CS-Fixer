=============================
Rule ``numeric_literal_case``
=============================

Number literals must be in correct case.

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

   -$foo = 3E14;
   -$bar = 7.6E-5;
   +$foo = 3e14;
   +$bar = 7.6e-5;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

