============================
Rule ``use_arrow_functions``
============================

Anonymous functions with return statement must use arrow functions.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when using ``isset()`` on outside variables that are not imported with
``use ()``.

Configuration
-------------

``one_liners_only``
~~~~~~~~~~~~~~~~~~~

Whether fix only one-liner function or all.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -foo(function ($a) use ($b) {
   -    return $a + $b;
   -});
   +foo(fn ($a) => $a + $b);

Example #2
~~~~~~~~~~

With configuration: ``['one_liners_only' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -bar(function ($a, $b) {
   -    return $a
   -        * $b;
   -});
   +bar(fn ($a, $b) => $a
   +        * $b);

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ with config:

  ``['one_liners_only' => false]``


References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\UseArrowFunctionsFixer <./../../../src/Fixer/FunctionNotation/UseArrowFunctionsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\UseArrowFunctionsFixerTest <./../../../tests/Fixer/FunctionNotation/UseArrowFunctionsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
