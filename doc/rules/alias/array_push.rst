===================
Rule ``array_push``
===================

Converts simple usages of ``array_push($x, $y);`` to ``$x[] = $y;``.

.. warning:: Using this rule is risky.

   Risky when the function ``array_push`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -array_push($x, $y);
   +$x[] = $y;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``array_push`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``array_push`` rule.
