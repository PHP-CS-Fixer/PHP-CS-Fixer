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

This rule is RISKY
~~~~~~~~~~~~~~~~~~

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDataProviderReturnTypeFixer <./../../../src/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitDataProviderReturnTypeFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
