==============================
Rule ``pow_to_exponentiation``
==============================

Converts ``pow`` to the ``**`` operator.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the function ``pow`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   - pow($a, 1);
   + $a** 1;

Rule sets
---------

The rule is part of the following rule sets:

@PHP56Migration:risky
  Using the `@PHP56Migration:risky <./../../ruleSets/PHP56MigrationRisky.rst>`_ rule set will enable the ``pow_to_exponentiation`` rule.

@PHP70Migration:risky
  Using the `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_ rule set will enable the ``pow_to_exponentiation`` rule.

@PHP71Migration:risky
  Using the `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ rule set will enable the ``pow_to_exponentiation`` rule.

@PHP74Migration:risky
  Using the `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ rule set will enable the ``pow_to_exponentiation`` rule.

@PHP80Migration:risky
  Using the `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ rule set will enable the ``pow_to_exponentiation`` rule.

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``pow_to_exponentiation`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``pow_to_exponentiation`` rule.
