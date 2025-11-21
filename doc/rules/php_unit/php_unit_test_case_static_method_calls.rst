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

Allowed values: ``'self'``, ``'static'`` and ``'this'``

Default value: ``'static'``

``methods``
~~~~~~~~~~~

Dictionary of ``method`` => ``call_type`` values that differ from the default
strategy.

Allowed types: ``array<string, string>``

Default value: ``[]``

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'10.0'``, ``'11.0'`` and ``'newest'``

Default value: ``'10.0'``

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
            static::assertTrue(false);
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
   -        static::assertTrue(false);
   +        $this->assertSame(1, 2);
   +        $this->assertSame(1, 2);
   +        $this->assertTrue(false);
        }
    }

Example #3
~~~~~~~~~~

With configuration: ``['methods' => ['assertTrue' => 'this']]``.

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
            static::assertSame(1, 2);
   -        static::assertTrue(false);
   +        static::assertSame(1, 2);
   +        static::assertSame(1, 2);
   +        $this->assertTrue(false);
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHPUnit110Migration:risky <./../../ruleSets/PHPUnit110MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '11.0']``

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ with config:

  ``['call_type' => 'self']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitTestCaseStaticMethodCallsFixer <./../../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitTestCaseStaticMethodCallsFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
