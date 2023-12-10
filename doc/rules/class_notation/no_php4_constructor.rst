============================
Rule ``no_php4_constructor``
============================

Convert PHP4-style constructors to ``__construct``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when old style constructor being fixed is overridden or overrides parent
one.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo
    {
   -    public function Foo($bar)
   +    public function __construct($bar)
        {
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ClassNotation\\NoPhp4ConstructorFixer <./../../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php>`_
