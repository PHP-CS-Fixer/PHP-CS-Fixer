===============================
Rule ``combine_nested_dirname``
===============================

Replace multiple nested calls of ``dirname`` by only one call with second
``$level`` parameter. Requires PHP >= 7.0.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

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

- `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_
- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\CombineNestedDirnameFixer <./../src/Fixer/FunctionNotation/CombineNestedDirnameFixer.php>`_
