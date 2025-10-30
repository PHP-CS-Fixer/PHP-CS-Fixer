==========================
Rule ``strict_comparison``
==========================

Comparisons should be strict.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Changing comparisons to strict might change code behaviour.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = 1== $b;
   +$a = 1=== $b;

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Strict\\StrictComparisonFixer <./../../../src/Fixer/Strict/StrictComparisonFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Strict\\StrictComparisonFixerTest <./../../../tests/Fixer/Strict/StrictComparisonFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
