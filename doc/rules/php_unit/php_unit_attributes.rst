============================
Rule ``php_unit_attributes``
============================

PHPUnit attributes must be used over their respective PHPDoc-based annotations.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``keep_annotations``.

Configuration
-------------

``keep_annotations``
~~~~~~~~~~~~~~~~~~~~

Whether to keep annotations or not. This may be helpful for projects that
support PHP before version 8 or PHPUnit before version 10.

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
    /**
   - * @covers \VendorName\Foo
     * @internal
     */
   +#[\PHPUnit\Framework\Attributes\CoversClass(\VendorName\Foo::class)]
    final class FooTest extends TestCase {
        /**
         * @param int $expected
         * @param int $actual
   -     * @dataProvider giveMeSomeData
   -     * @requires PHP 8.0
         */
   +    #[\PHPUnit\Framework\Attributes\DataProvider('giveMeSomeData')]
   +    #[\PHPUnit\Framework\Attributes\RequiresPhp('>= 8.0')]
        public function testSomething($expected, $actual) {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['keep_annotations' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @covers \VendorName\Foo
     * @internal
     */
   +#[\PHPUnit\Framework\Attributes\CoversClass(\VendorName\Foo::class)]
    final class FooTest extends TestCase {
        /**
         * @param int $expected
         * @param int $actual
         * @dataProvider giveMeSomeData
         * @requires PHP 8.0
         */
   +    #[\PHPUnit\Framework\Attributes\DataProvider('giveMeSomeData')]
   +    #[\PHPUnit\Framework\Attributes\RequiresPhp('>= 8.0')]
        public function testSomething($expected, $actual) {}
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitAttributesFixer <./../../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitAttributesFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitAttributesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
