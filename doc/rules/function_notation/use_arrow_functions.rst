============================
Rule ``use_arrow_functions``
============================

Anonymous functions with return as the only statement must use arrow functions.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when using ``isset()`` on outside variables that are not imported with
``use ()``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -foo(function ($a) use ($b) {
   -    return $a + $b;
   -});
   +foo(fn ($a) => $a + $b);

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\UseArrowFunctionsFixer <./../../../src/Fixer/FunctionNotation/UseArrowFunctionsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\UseArrowFunctionsFixerTest <./../../../tests/Fixer/FunctionNotation/UseArrowFunctionsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
