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

- `@PHP7.4Migration:risky <./../../ruleSets/PHP7.4MigrationRisky.rst>`_
- `@PHP8.0Migration:risky <./../../ruleSets/PHP8.0MigrationRisky.rst>`_
- `@PHP8.2Migration:risky <./../../ruleSets/PHP8.2MigrationRisky.rst>`_
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\UseArrowFunctionsFixer <./../../../src/Fixer/FunctionNotation/UseArrowFunctionsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\UseArrowFunctionsFixerTest <./../../../tests/Fixer/FunctionNotation/UseArrowFunctionsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
