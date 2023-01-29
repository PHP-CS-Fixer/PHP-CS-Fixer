======================================
Rule ``php_unit_data_provider_static``
======================================

Data providers must be static.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Fixer could be risky if one is calling data provider function dynamically.

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
   -    public function provideSomethingCases() {}
   +    public static function provideSomethingCases() {}
    }

Rule sets
---------

The rule is part of the following rule set:

@PHPUnit100Migration:risky
  Using the `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ rule set will enable the ``php_unit_data_provider_static`` rule.
