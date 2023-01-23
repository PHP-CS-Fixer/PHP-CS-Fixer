==================================
Rule ``numeric_literal_separator``
==================================

Adds separators to numeric literals of any kind.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $var = 123456;
   +<?php $var = 123_456;
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Basic\\NumericLiteralSeparatorFixer <./../../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Basic\\NumericLiteralSeparatorFixerTest <./../../../tests/Fixer/Basic/NumericLiteralSeparatorFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
