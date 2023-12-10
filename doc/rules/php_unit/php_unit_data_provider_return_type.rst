===========================================
Rule ``php_unit_data_provider_return_type``
===========================================

The return type of PHPUnit data provider must be ``iterable``.

Description
-----------

Data provider must return ``iterable``, either an array of arrays or an object
that implements the ``Traversable`` interface.

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

The rule is part of the following rule set:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDataProviderReturnTypeFixer <./../../../src/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixer.php>`_
