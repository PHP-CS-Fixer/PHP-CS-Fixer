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

- `@PHP5x6Migration:risky <./../../ruleSets/PHP5x6MigrationRisky.rst>`_
- `@PHP7x0Migration:risky <./../../ruleSets/PHP7x0MigrationRisky.rst>`_
- `@PHP7x1Migration:risky <./../../ruleSets/PHP7x1MigrationRisky.rst>`_
- `@PHP7x4Migration:risky <./../../ruleSets/PHP7x4MigrationRisky.rst>`_
- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_
- `@PHP8x1Migration:risky <./../../ruleSets/PHP8x1MigrationRisky.rst>`_
- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP8x3Migration:risky <./../../ruleSets/PHP8x3MigrationRisky.rst>`_
- `@PHP8x4Migration:risky <./../../ruleSets/PHP8x4MigrationRisky.rst>`_
- `@PHP8x5Migration:risky <./../../ruleSets/PHP8x5MigrationRisky.rst>`_
- `@PHP56Migration:risky <./../../ruleSets/PHP56MigrationRisky.rst>`_ *(deprecated)*
- `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_ *(deprecated)*
- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ *(deprecated)*
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ *(deprecated)*
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ *(deprecated)*
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\PowToExponentiationFixer <./../../../src/Fixer/Alias/PowToExponentiationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\PowToExponentiationFixerTest <./../../../tests/Fixer/Alias/PowToExponentiationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
