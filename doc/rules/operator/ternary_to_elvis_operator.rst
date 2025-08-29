==================================
Rule ``ternary_to_elvis_operator``
==================================

Use the Elvis operator ``?:`` where possible.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when relying on functions called on both sides of the ``?`` operator.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = $foo ? $foo : 1;
   +$foo = $foo ?  : 1;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $foo = $bar[a()] ? $bar[a()] : 1; # "risky" sample, "a()" only gets called once after fixing
   +<?php $foo = $bar[a()] ?  : 1; # "risky" sample, "a()" only gets called once after fixing

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\TernaryToElvisOperatorFixer <./../../../src/Fixer/Operator/TernaryToElvisOperatorFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\TernaryToElvisOperatorFixerTest <./../../../tests/Fixer/Operator/TernaryToElvisOperatorFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
