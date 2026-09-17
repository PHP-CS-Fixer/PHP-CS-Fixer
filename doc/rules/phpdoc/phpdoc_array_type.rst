==========================
Rule ``phpdoc_array_type``
==========================

PHPDoc ``array<T>`` type must be used instead of ``T[]``.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when using ``T[]`` in union types.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param int[] $x
   - * @param string[][] $y
   + * @param array<int> $x
   + * @param array<array<string>> $y
     */

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocArrayTypeFixer <./../../../src/Fixer/Phpdoc/PhpdocArrayTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocArrayTypeFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocArrayTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
