=====================
Rule ``implode_call``
=====================

Function ``implode`` must be called with 2 arguments in the documented order.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when the function ``implode`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -implode($pieces, '');
   +implode('', $pieces);

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -implode($pieces);
   +implode('', $pieces);

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP7x4Migration:risky <./../../ruleSets/PHP7x4MigrationRisky.rst>`_
- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_
- `@PHP8x1Migration:risky <./../../ruleSets/PHP8x1MigrationRisky.rst>`_
- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP8x3Migration:risky <./../../ruleSets/PHP8x3MigrationRisky.rst>`_
- `@PHP8x4Migration:risky <./../../ruleSets/PHP8x4MigrationRisky.rst>`_
- `@PHP8x5Migration:risky <./../../ruleSets/PHP8x5MigrationRisky.rst>`_
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ *(deprecated)*
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ *(deprecated)*
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\ImplodeCallFixer <./../../../src/Fixer/FunctionNotation/ImplodeCallFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\ImplodeCallFixerTest <./../../../tests/Fixer/FunctionNotation/ImplodeCallFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
