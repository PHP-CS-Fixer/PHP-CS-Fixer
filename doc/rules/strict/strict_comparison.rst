==========================
Rule ``strict_comparison``
==========================

Comparisons should be strict.

.. warning:: Using this rule is risky.

   Changing comparisons to strict might change code behavior.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$a = 1== $b;
   +$a = 1=== $b;

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``strict_comparison`` rule.
