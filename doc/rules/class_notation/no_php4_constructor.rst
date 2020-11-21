============================
Rule ``no_php4_constructor``
============================

Convert PHP4-style constructors to ``__construct``.

.. warning:: Using this rule is risky.

   Risky when old style constructor being fixed is overridden or overrides
   parent one.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,7 @@
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

@PHP80Migration:risky
  Using the `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ rule set will enable the ``no_php4_constructor`` rule.

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``no_php4_constructor`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``no_php4_constructor`` rule.
