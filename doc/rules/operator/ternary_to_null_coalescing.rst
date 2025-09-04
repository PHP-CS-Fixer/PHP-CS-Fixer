===================================
Rule ``ternary_to_null_coalescing``
===================================

Use ``null`` coalescing operator ``??`` where possible.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = isset($a) ? $a : $b;
   +$sample = $a ?? $b;

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP7x0Migration <./../../ruleSets/PHP7x0Migration.rst>`_
- `@PHP7x1Migration <./../../ruleSets/PHP7x1Migration.rst>`_
- `@PHP7x3Migration <./../../ruleSets/PHP7x3Migration.rst>`_
- `@PHP7x4Migration <./../../ruleSets/PHP7x4Migration.rst>`_
- `@PHP8x0Migration <./../../ruleSets/PHP8x0Migration.rst>`_
- `@PHP8x1Migration <./../../ruleSets/PHP8x1Migration.rst>`_
- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_
- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_
- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_
- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_
- `@PHP70Migration <./../../ruleSets/PHP70Migration.rst>`_
- `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\TernaryToNullCoalescingFixer <./../../../src/Fixer/Operator/TernaryToNullCoalescingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\TernaryToNullCoalescingFixerTest <./../../../tests/Fixer/Operator/TernaryToNullCoalescingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
