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

- `@PHP7.0Migration <./../../ruleSets/PHP7.0Migration.rst>`_
- `@PHP7.1Migration <./../../ruleSets/PHP7.1Migration.rst>`_
- `@PHP7.3Migration <./../../ruleSets/PHP7.3Migration.rst>`_
- `@PHP7.4Migration <./../../ruleSets/PHP7.4Migration.rst>`_
- `@PHP8.0Migration <./../../ruleSets/PHP8.0Migration.rst>`_
- `@PHP8.1Migration <./../../ruleSets/PHP8.1Migration.rst>`_
- `@PHP8.2Migration <./../../ruleSets/PHP8.2Migration.rst>`_
- `@PHP8.3Migration <./../../ruleSets/PHP8.3Migration.rst>`_
- `@PHP8.4Migration <./../../ruleSets/PHP8.4Migration.rst>`_
- `@PHP8.5Migration <./../../ruleSets/PHP8.5Migration.rst>`_
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
