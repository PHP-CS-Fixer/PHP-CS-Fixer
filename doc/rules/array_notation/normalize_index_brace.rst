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

- `@PHP7x4Migration <./../../ruleSets/PHP7x4Migration.rst>`_
- `@PHP8x0Migration <./../../ruleSets/PHP8x0Migration.rst>`_
- `@PHP8x1Migration <./../../ruleSets/PHP8x1Migration.rst>`_
- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_
- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_
- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_
- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ *(deprecated)*
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ *(deprecated)*
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ *(deprecated)*
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ *(deprecated)*
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_ *(deprecated)*
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ *(deprecated)*
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ *(deprecated)*
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\NormalizeIndexBraceFixer <./../../../src/Fixer/ArrayNotation/NormalizeIndexBraceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\NormalizeIndexBraceFixerTest <./../../../tests/Fixer/ArrayNotation/NormalizeIndexBraceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
