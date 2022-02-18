===============================================
Rule ``php_unit_test_case_static_method_calls``
===============================================

Calls to ``PHPUnit\Framework\TestCase`` static methods must all be of the same
type, either ``$this->``, ``self::`` or ``static::``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when PHPUnit methods are overridden or not accessible, or when project has
PHPUnit incompatibilities.

Configuration
-------------

``call_type``
~~~~~~~~~~~~~

The call type to use for referring to PHPUnit methods.

Allowed values: ``'self'``, ``'static'``, ``'this'``

Default value: ``'static'``

``methods``
~~~~~~~~~~~

Dictionary of ``method`` => ``call_type`` values that differ from the default
strategy.

Allowed types: ``array``

Default value: ``[]``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testMe()
        {
   -        $this->assertSame(1, 2);
   -        self::assertSame(1, 2);
   +        static::assertSame(1, 2);
   +        static::assertSame(1, 2);
            static::assertSame(1, 2);
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['call_type' => 'this']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testMe()
        {
            $this->assertSame(1, 2);
   -        self::assertSame(1, 2);
   -        static::assertSame(1, 2);
   +        $this->assertSame(1, 2);
   +        $this->assertSame(1, 2);
        }
    }

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``php_unit_test_case_static_method_calls`` rule with the default config.
