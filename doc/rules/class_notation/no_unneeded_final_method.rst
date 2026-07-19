=================================
Rule ``no_unneeded_final_method``
=================================

Removes ``final`` from methods where possible.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when child class overrides a ``private`` method.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``private_methods``.

Configuration
-------------

``private_methods``
~~~~~~~~~~~~~~~~~~~

Private methods of non-``final`` classes must not be declared ``final``.

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

Example #2
~~~~~~~~~~

With configuration: ``['private_methods' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Foo
    {
   -    final private function baz() {}
   +    private function baz() {}
    }

    class Bar
    {
        final private function bar1() {}
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_
- `@PHP8x1Migration:risky <./../../ruleSets/PHP8x1MigrationRisky.rst>`_
- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP8x3Migration:risky <./../../ruleSets/PHP8x3MigrationRisky.rst>`_
- `@PHP8x4Migration:risky <./../../ruleSets/PHP8x4MigrationRisky.rst>`_
- `@PHP8x5Migration:risky <./../../ruleSets/PHP8x5MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ *(deprecated)*
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\NoUnneededFinalMethodFixer <./../../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\NoUnneededFinalMethodFixerTest <./../../../tests/Fixer/ClassNotation/NoUnneededFinalMethodFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
