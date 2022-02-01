===============================
Rule ``combine_nested_dirname``
===============================

Replace multiple nested calls of ``dirname`` by only one call with second
``$level`` parameter. Requires PHP >= 7.0.

.. warning:: Using this rule is risky.

   Risky when the function ``dirname`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -dirname(dirname(dirname($path)));
   +dirname($path, 3);

Rule sets
---------

The rule is part of the following rule sets:

@PHP70Migration:risky
  Using the `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_ rule set will enable the ``combine_nested_dirname`` rule.

@PHP71Migration:risky
  Using the `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ rule set will enable the ``combine_nested_dirname`` rule.

@PHP74Migration:risky
  Using the `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ rule set will enable the ``combine_nested_dirname`` rule.

@PHP80Migration:risky
  Using the `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ rule set will enable the ``combine_nested_dirname`` rule.

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``combine_nested_dirname`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``combine_nested_dirname`` rule.
