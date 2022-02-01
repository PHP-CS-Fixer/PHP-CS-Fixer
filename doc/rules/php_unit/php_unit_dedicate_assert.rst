=================================
Rule ``php_unit_dedicate_assert``
=================================

PHPUnit assertions like ``assertInternalType``, ``assertFileExists``, should be
used over ``assertTrue``.

.. warning:: Using this rule is risky.

   Fixer could be risky if one is overriding PHPUnit's native methods.

Configuration
-------------

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'3.0'``, ``'3.5'``, ``'5.0'``, ``'5.6'``, ``'newest'``

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
        public function testSomeTest()
        {
   -        $this->assertTrue(is_float( $a), "my message");
   -        $this->assertTrue(is_nan($a));
   +        $this->assertInternalType('float', $a, "my message");
   +        $this->assertNan($a);
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['target' => '5.6']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testSomeTest()
        {
   -        $this->assertTrue(is_dir($a));
   -        $this->assertTrue(is_writable($a));
   -        $this->assertTrue(is_readable($a));
   +        $this->assertDirectoryExists($a);
   +        $this->assertIsWritable($a);
   +        $this->assertIsReadable($a);
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PHPUnit30Migration:risky
  Using the `@PHPUnit30Migration:risky <./../../ruleSets/PHPUnit30MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '3.0']``

@PHPUnit32Migration:risky
  Using the `@PHPUnit32Migration:risky <./../../ruleSets/PHPUnit32MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '3.0']``

@PHPUnit35Migration:risky
  Using the `@PHPUnit35Migration:risky <./../../ruleSets/PHPUnit35MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '3.5']``

@PHPUnit43Migration:risky
  Using the `@PHPUnit43Migration:risky <./../../ruleSets/PHPUnit43MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '3.5']``

@PHPUnit48Migration:risky
  Using the `@PHPUnit48Migration:risky <./../../ruleSets/PHPUnit48MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '3.5']``

@PHPUnit50Migration:risky
  Using the `@PHPUnit50Migration:risky <./../../ruleSets/PHPUnit50MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '5.0']``

@PHPUnit52Migration:risky
  Using the `@PHPUnit52Migration:risky <./../../ruleSets/PHPUnit52MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '5.0']``

@PHPUnit54Migration:risky
  Using the `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '5.0']``

@PHPUnit55Migration:risky
  Using the `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '5.0']``

@PHPUnit56Migration:risky
  Using the `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit57Migration:risky
  Using the `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit60Migration:risky
  Using the `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit75Migration:risky
  Using the `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit84Migration:risky
  Using the `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ rule set will enable the ``php_unit_dedicate_assert`` rule with the config below:

  ``['target' => '5.6']``
