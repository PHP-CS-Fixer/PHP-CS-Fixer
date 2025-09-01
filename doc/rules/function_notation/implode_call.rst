=====================
Rule ``implode_call``
=====================

Function ``implode`` must be called with 2 arguments in the documented order.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

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

- `@PHP7.4Migration:risky <./../../ruleSets/PHP7.4MigrationRisky.rst>`_
- `@PHP8.0Migration:risky <./../../ruleSets/PHP8.0MigrationRisky.rst>`_
- `@PHP8.2Migration:risky <./../../ruleSets/PHP8.2MigrationRisky.rst>`_
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\ImplodeCallFixer <./../../../src/Fixer/FunctionNotation/ImplodeCallFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\ImplodeCallFixerTest <./../../../tests/Fixer/FunctionNotation/ImplodeCallFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
