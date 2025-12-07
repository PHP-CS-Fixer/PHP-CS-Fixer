======================
Rule ``php_unit_mock``
======================

Usages of ``->getMock`` and ``->getMockWithoutInvokingTheOriginalConstructor``
methods MUST be replaced by ``->createMock`` or ``->createPartialMock`` methods.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when PHPUnit classes are overridden or not accessible, or when project has
PHPUnit incompatibilities.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``target``.

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

- `@PHPUnit5x4Migration:risky <./../../ruleSets/PHPUnit5x4MigrationRisky.rst>`_ with config:

  ``['target' => '5.4']``

- `@PHPUnit5x5Migration:risky <./../../ruleSets/PHPUnit5x5MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit5x6Migration:risky <./../../ruleSets/PHPUnit5x6MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit5x7Migration:risky <./../../ruleSets/PHPUnit5x7MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit6x0Migration:risky <./../../ruleSets/PHPUnit6x0MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit7x5Migration:risky <./../../ruleSets/PHPUnit7x5MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit8x4Migration:risky <./../../ruleSets/PHPUnit8x4MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit9x1Migration:risky <./../../ruleSets/PHPUnit9x1MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit10x0Migration:risky <./../../ruleSets/PHPUnit10x0MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit11x0Migration:risky <./../../ruleSets/PHPUnit11x0MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit12x5Migration:risky <./../../ruleSets/PHPUnit12x5MigrationRisky.rst>`_ with config:

  ``['target' => '5.5']``

- `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.4']``

- `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.5']``

- `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.5']``

- `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.5']``

- `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.5']``

- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.5']``

- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.5']``

- `@PHPUnit91Migration:risky <./../../ruleSets/PHPUnit91MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.5']``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.5']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitMockFixer <./../../../src/Fixer/PhpUnit/PhpUnitMockFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitMockFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitMockFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
