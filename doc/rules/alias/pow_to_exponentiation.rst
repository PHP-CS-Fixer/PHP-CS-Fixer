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

- `@PHP56Migration:risky <./../../ruleSets/PHP56MigrationRisky.rst>`_
- `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_
- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\PowToExponentiationFixer <./../../../src/Fixer/Alias/PowToExponentiationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\PowToExponentiationFixerTest <./../../../tests/Fixer/Alias/PowToExponentiationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
