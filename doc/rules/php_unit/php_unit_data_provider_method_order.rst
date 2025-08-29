============================================
Rule ``php_unit_data_provider_method_order``
============================================

Data provider method must be placed after/before the last/first test where used.

Configuration
-------------

``placement``
~~~~~~~~~~~~~

Where to place the data provider relative to the test where used.

Allowed values: ``'after'`` and ``'before'``

Default value: ``'after'``

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
   -    public function dataProvider() {}
        /**
         * @dataProvider dataProvider
         */
        public function testSomething($expected, $actual) {}
   +    public function dataProvider() {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['placement' => 'before']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class FooTest extends TestCase {
   +    public function dataProvider() {}
        /**
         * @dataProvider dataProvider
         */
        public function testSomething($expected, $actual) {}
   -    public function dataProvider() {}
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDataProviderMethodOrderFixer <./../../../src/Fixer/PhpUnit/PhpUnitDataProviderMethodOrderFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitDataProviderMethodOrderFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitDataProviderMethodOrderFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
