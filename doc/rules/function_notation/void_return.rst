====================
Rule ``void_return``
====================

Add ``void`` return type to functions with missing or empty return statements,
but priority is given to ``@return`` annotations. Requires PHP >= 7.1.

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

- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\VoidReturnFixer <./../../../src/Fixer/FunctionNotation/VoidReturnFixer.php>`_
