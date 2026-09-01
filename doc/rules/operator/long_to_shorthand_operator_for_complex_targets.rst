=======================================================
Rule ``long_to_shorthand_operator_for_complex_targets``
=======================================================

Shorthand notation for operators should be used if possible, also for
non-plain-variable assignment targets (member access, offsets, ...).

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky, because the target is evaluated once instead of twice, which changes
behaviour when evaluating it has a side effect (e.g. ``$a[f()] = $a[f()] + 1;``
calls ``f()`` once after the change) or invokes a magic accessor (e.g.
``$obj->m->x`` when ``m`` is a ``__get``); and because an assign-op on a string
offset (e.g. ``$text[0] = $text[0] & "\x7F";``) fails at runtime with "Cannot
use assign-op operators with string offsets".

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$this->value = $this->value + 1;
   -$numbers[0] = $numbers[0] * 2;
   +$this->value += 1;
   +$numbers[0] *= 2;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\LongToShorthandOperatorForComplexTargetsFixer <./../../../src/Fixer/Operator/LongToShorthandOperatorForComplexTargetsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\LongToShorthandOperatorForComplexTargetsFixerTest <./../../../tests/Fixer/Operator/LongToShorthandOperatorForComplexTargetsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
