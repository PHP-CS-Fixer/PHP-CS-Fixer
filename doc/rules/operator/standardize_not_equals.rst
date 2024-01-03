===============================
Rule ``standardize_not_equals``
===============================

Replace all ``<>`` with ``!=``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = $b <> $c;
   +$a = $b != $c;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\StandardizeNotEqualsFixer <./../../../src/Fixer/Operator/StandardizeNotEqualsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\StandardizeNotEqualsFixerTest <./../../../tests/Fixer/Operator/StandardizeNotEqualsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
