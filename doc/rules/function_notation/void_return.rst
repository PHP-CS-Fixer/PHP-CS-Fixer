====================
Rule ``void_return``
====================

Add ``void`` return type to functions with missing or empty return statements,
but priority is given to ``@return`` annotations.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Modifies the signature of functions.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo($a) {};
   +function foo($a): void {};

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP7x1Migration:risky <./../../ruleSets/PHP7x1MigrationRisky.rst>`_
- `@PHP7x4Migration:risky <./../../ruleSets/PHP7x4MigrationRisky.rst>`_
- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_
- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ *(deprecated)*
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ *(deprecated)*
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ *(deprecated)*
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\VoidReturnFixer <./../../../src/Fixer/FunctionNotation/VoidReturnFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\VoidReturnFixerTest <./../../../tests/Fixer/FunctionNotation/VoidReturnFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
