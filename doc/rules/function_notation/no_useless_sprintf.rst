===========================
Rule ``no_useless_sprintf``
===========================

There must be no ``sprintf`` calls with only the first argument.

.. warning:: Using this rule is risky.

   Risky when if the ``sprintf`` function is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$foo = sprintf('bar');
   +$foo = 'bar';

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``no_useless_sprintf`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``no_useless_sprintf`` rule.
