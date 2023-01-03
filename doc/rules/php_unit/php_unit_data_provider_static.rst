======================================
Rule ``php_unit_data_provider_static``
======================================

Data providers must be static.

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

The rule is part of the following rule sets:

@PHPUnit100Migration:risky
  Using the `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ rule set will enable the ``php_unit_data_provider_static`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``php_unit_data_provider_static`` rule.
