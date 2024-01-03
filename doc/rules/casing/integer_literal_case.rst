=============================
Rule ``integer_literal_case``
=============================

Integer literals must be in correct case.

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

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Casing\\IntegerLiteralCaseFixer <./../../../src/Fixer/Casing/IntegerLiteralCaseFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Casing\\IntegerLiteralCaseFixerTest <./../../../tests/Fixer/Casing/IntegerLiteralCaseFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
