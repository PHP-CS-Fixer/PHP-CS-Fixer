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

- `@PHP8x1Migration <./../../ruleSets/PHP8x1Migration.rst>`_
- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_
- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_
- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_
- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Basic\\OctalNotationFixer <./../../../src/Fixer/Basic/OctalNotationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Basic\\OctalNotationFixerTest <./../../../tests/Fixer/Basic/OctalNotationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
