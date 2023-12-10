=======================
Rule ``octal_notation``
=======================

Literal octal must be in ``0o`` notation.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $foo = 0123;
   +<?php $foo = 0o123;

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Basic\\OctalNotationFixer <./../../../src/Fixer/Basic/OctalNotationFixer.php>`_
