===============================================
Rule ``php_unit_dedicate_assert_internal_type``
===============================================

PHPUnit assertions like ``assertIsArray`` should be used over
``assertInternalType``.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when PHPUnit methods are overridden or when project has PHPUnit
incompatibilities.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``target``.

Configuration
-------------

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'7.5'`` and ``'newest'``

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
    final class MyTest extends \PHPUnit\Framework\TestCase
    {
        public function testMe()
        {
   -        $this->assertInternalType("array", $var);
   -        $this->assertInternalType("boolean", $var);
   +        $this->assertIsArray($var);
   +        $this->assertIsBool($var);
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['target' => '7.5']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit\Framework\TestCase
    {
        public function testMe()
        {
   -        $this->assertInternalType("array", $var);
   -        $this->assertInternalType("boolean", $var);
   +        $this->assertIsArray($var);
   +        $this->assertIsBool($var);
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHPUnit7x5Migration:risky <./../../ruleSets/PHPUnit7x5MigrationRisky.rst>`_ with config:

  ``['target' => '7.5']``

- `@PHPUnit8x4Migration:risky <./../../ruleSets/PHPUnit8x4MigrationRisky.rst>`_ with config:

  ``['target' => '7.5']``

- `@PHPUnit9x1Migration:risky <./../../ruleSets/PHPUnit9x1MigrationRisky.rst>`_ with config:

  ``['target' => '7.5']``

- `@PHPUnit10x0Migration:risky <./../../ruleSets/PHPUnit10x0MigrationRisky.rst>`_ with config:

  ``['target' => '7.5']``

- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '7.5']``

- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '7.5']``

- `@PHPUnit91Migration:risky <./../../ruleSets/PHPUnit91MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '7.5']``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '7.5']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDedicateAssertInternalTypeFixer <./../../../src/Fixer/PhpUnit/PhpUnitDedicateAssertInternalTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitDedicateAssertInternalTypeFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitDedicateAssertInternalTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
