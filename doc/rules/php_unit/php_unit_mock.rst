======================
Rule ``php_unit_mock``
======================

Usages of ``->getMock`` and ``->getMockWithoutInvokingTheOriginalConstructor``
methods MUST be replaced by ``->createMock`` or ``->createPartialMock`` methods.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when PHPUnit classes are overridden or not accessible, or when project has
PHPUnit incompatibilities.

Configuration
-------------

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'5.4'``, ``'5.5'`` and ``'newest'``

Default value: ``'newest'``

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
        public function testFoo()
        {
   -        $mock = $this->getMockWithoutInvokingTheOriginalConstructor("Foo");
   -        $mock1 = $this->getMock("Foo");
   -        $mock1 = $this->getMock("Bar", ["aaa"]);
   +        $mock = $this->createMock("Foo");
   +        $mock1 = $this->createMock("Foo");
   +        $mock1 = $this->createPartialMock("Bar", ["aaa"]);
            $mock1 = $this->getMock("Baz", ["aaa"], ["argument"]); // version with more than 2 params is not supported
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['target' => '5.4']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
   -        $mock1 = $this->getMock("Foo");
   +        $mock1 = $this->createMock("Foo");
            $mock1 = $this->getMock("Bar", ["aaa"]); // version with multiple params is not supported
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ with config:

  ``['target' => '5.4']``

- `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``


Source class
------------

`PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitMockFixer <./../../../src/Fixer/PhpUnit/PhpUnitMockFixer.php>`_
