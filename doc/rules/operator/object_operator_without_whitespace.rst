===========================================
Rule ``object_operator_without_whitespace``
===========================================

There should not be space before or after object operators ``->`` and ``?->``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a  ->  b;
   +<?php $a->b;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\ObjectOperatorWithoutWhitespaceFixer <./../../../src/Fixer/Operator/ObjectOperatorWithoutWhitespaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\ObjectOperatorWithoutWhitespaceFixerTest <./../../../tests/Fixer/Operator/ObjectOperatorWithoutWhitespaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
