=================================
Rule ``no_unneeded_final_method``
=================================

A ``final`` class must not have ``final`` methods and ``private`` methods must
not be ``final``.

.. warning:: Using this rule is risky.

   Risky when child class overrides a ``private`` method.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,12 +1,12 @@
    <?php
    final class Foo
    {
   -    final public function foo1() {}
   -    final protected function bar() {}
   -    final private function baz() {}
   +    public function foo1() {}
   +    protected function bar() {}
   +    private function baz() {}
    }

    class Bar
    {
   -    final private function bar1() {}
   +    private function bar1() {}
    }

Rule sets
---------

The rule is part of the following rule sets:

@PHP80Migration:risky
  Using the `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ rule set will enable the ``no_unneeded_final_method`` rule.

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``no_unneeded_final_method`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``no_unneeded_final_method`` rule.
