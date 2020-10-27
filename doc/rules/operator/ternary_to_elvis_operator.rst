==================================
Rule ``ternary_to_elvis_operator``
==================================

Use the Elvis operator ``?:`` where possible.

.. warning:: Using this rule is risky.

   Risky when relying on functions called on both sides of the ``?`` operator.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$foo = $foo ? $foo : 1;
   +$foo = $foo ?  : 1;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $foo = $bar[a()] ? $bar[a()] : 1; # "risky" sample, "a()" only gets called once after fixing
   +<?php $foo = $bar[a()] ?  : 1; # "risky" sample, "a()" only gets called once after fixing

Rule sets
---------

The rule is part of the following rule sets:

@Symfony:risky
  Using the ``@Symfony:risky`` rule set will enable the ``ternary_to_elvis_operator`` rule.

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``ternary_to_elvis_operator`` rule.
