===================================
Rule ``ternary_to_null_coalescing``
===================================

Use ``null`` coalescing operator ``??`` where possible. Requires PHP >= 7.0.

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

- `@PHP70Migration <./../../ruleSets/PHP70Migration.rst>`_
- `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\TernaryToNullCoalescingFixer <./../../../src/Fixer/Operator/TernaryToNullCoalescingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\TernaryToNullCoalescingFixerTest <./../../../tests/Fixer/Operator/TernaryToNullCoalescingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
