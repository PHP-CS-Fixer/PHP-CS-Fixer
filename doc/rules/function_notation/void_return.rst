====================
Rule ``void_return``
====================

Add ``void`` return type to functions with missing or empty return statements,
but priority is given to ``@return`` annotations. Requires PHP >= 7.1.

.. warning:: Using this rule is risky.

   Modifies the signature of functions.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -function foo($a) {};
   +function foo($a): void {};

Rule sets
---------

The rule is part of the following rule sets:

@PHP71Migration:risky
  Using the `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ rule set will enable the ``void_return`` rule.

@PHP74Migration:risky
  Using the `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ rule set will enable the ``void_return`` rule.

@PHP80Migration:risky
  Using the `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ rule set will enable the ``void_return`` rule.
