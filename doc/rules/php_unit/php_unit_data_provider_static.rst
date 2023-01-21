======================================
Rule ``php_unit_data_provider_static``
======================================

Data providers must be static.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Fixer could be risky if one is calling data provider function dynamically.

Configuration
-------------

``force``
~~~~~~~~~

whether to make static data providers having dynamic class calls

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

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

Example #2
~~~~~~~~~~

With configuration: ``['force' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class FooTest extends TestCase {
        /**
         * @dataProvider provideSomethingCases
         */
        public function testSomething($expected, $actual) {}
   -    public function provideSomethingCases() { $this->getData(); }
   +    public static function provideSomethingCases() { $this->getData(); }
    }

Rule sets
---------

The rule is part of the following rule set:

@PHPUnit100Migration:risky
  Using the `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ rule set will enable the ``php_unit_data_provider_static`` rule with the default config.
