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
