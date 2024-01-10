==============================
Rule ``normalize_index_brace``
==============================

Array index should always be written by using square braces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -echo $sample{$index};
   +echo $sample[$index];

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\NormalizeIndexBraceFixer <./../../../src/Fixer/ArrayNotation/NormalizeIndexBraceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\NormalizeIndexBraceFixerTest <./../../../tests/Fixer/ArrayNotation/NormalizeIndexBraceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
