====================================
Rule ``php_unit_data_provider_name``
====================================

Data provider names must match the name of the test.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Fixer could be risky if one is calling data provider by name as function.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``prefix``, ``suffix``.

Configuration
-------------

``prefix``
~~~~~~~~~~

Prefix that replaces "test".

Allowed types: ``string``

Default value: ``'provide'``

``suffix``
~~~~~~~~~~

Suffix to be present at the end.

Allowed types: ``string``

Default value: ``'Cases'``

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
   -     * @dataProvider dataProvider
   +     * @dataProvider provideSomethingCases
         */
        public function testSomething($expected, $actual) {}
   -    public function dataProvider() {}
   +    public function provideSomethingCases() {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['prefix' => 'data_', 'suffix' => '']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class FooTest extends TestCase {
        /**
   -     * @dataProvider dt_prvdr_ftr
   +     * @dataProvider data_feature
         */
        public function test_feature($expected, $actual) {}
   -    public function dt_prvdr_ftr() {}
   +    public function data_feature() {}
    }

Example #3
~~~~~~~~~~

With configuration: ``['prefix' => 'provides', 'suffix' => 'Data']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class FooTest extends TestCase {
        /**
         * @dataProvider dataProviderUsedInMultipleTests
         */
        public function testA($expected, $actual) {}
        /**
         * @dataProvider dataProviderUsedInMultipleTests
         */
        public function testB($expected, $actual) {}
        /**
   -     * @dataProvider dataProviderUsedInSingleTest
   +     * @dataProvider providesCData
         */
        public function testC($expected, $actual) {}
        /**
         * @dataProvider dataProviderUsedAsFirstInTest
         * @dataProvider dataProviderUsedAsSecondInTest
         */
        public function testD($expected, $actual) {}

        public function dataProviderUsedInMultipleTests() {}
   -    public function dataProviderUsedInSingleTest() {}
   +    public function providesCData() {}
        public function dataProviderUsedAsFirstInTest() {}
        public function dataProviderUsedAsSecondInTest() {}
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDataProviderNameFixer <./../../../src/Fixer/PhpUnit/PhpUnitDataProviderNameFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitDataProviderNameFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitDataProviderNameFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
