===========================================
Rule ``php_unit_data_provider_return_type``
===========================================

The type returned by data provider must be ``iterable``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when relying on signature of the data provider.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class FooTest extends TestCase {
        /**
         * @dataProvider provideSomethingCases
         */
        public function testSomething($expected, $actual) {}
   -    public function provideSomethingCases(): array {}
   +    public function provideSomethingCases(): iterable {}
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class FooTest extends TestCase {
        /**
         * @dataProvider provideSomethingCases
         */
        public function testSomething($expected, $actual) {}
   -    public function provideSomethingCases() {}
   +    public function provideSomethingCases(): iterable {}
    }

Rule sets
---------

The rule is part of the following rule sets:

@PHPUnit60Migration:risky
  Using the `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ rule set will enable the ``php_unit_data_provider_return_type`` rule.

@PHPUnit75Migration:risky
  Using the `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ rule set will enable the ``php_unit_data_provider_return_type`` rule.

@PHPUnit84Migration:risky
  Using the `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ rule set will enable the ``php_unit_data_provider_return_type`` rule.

@PHPUnit100Migration:risky
  Using the `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ rule set will enable the ``php_unit_data_provider_return_type`` rule.

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``php_unit_data_provider_return_type`` rule.
