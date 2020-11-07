=====================
Rule ``dir_constant``
=====================

Replaces ``dirname(__FILE__)`` expression with equivalent ``__DIR__`` constant.

.. warning:: Using this rule is risky.

   Risky when the function ``dirname`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$a = dirname(__FILE__);
   +$a = __DIR__;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``dir_constant`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``dir_constant`` rule.
