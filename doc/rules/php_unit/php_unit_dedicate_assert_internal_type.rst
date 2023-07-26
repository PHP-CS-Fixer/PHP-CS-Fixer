===============================================
Rule ``php_unit_dedicate_assert_internal_type``
===============================================

PHPUnit assertions like ``assertIsArray`` should be used over
``assertInternalType``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when PHPUnit methods are overridden or when project has PHPUnit
incompatibilities.

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

- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ with config:

  ``['target' => '7.5']``

- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ with config:

  ``['target' => '7.5']``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ with config:

  ``['target' => '7.5']``


