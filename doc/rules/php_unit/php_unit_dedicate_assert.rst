=================================
Rule ``php_unit_dedicate_assert``
=================================

PHPUnit assertions like ``assertInternalType``, ``assertFileExists``, should be
used over ``assertTrue``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Fixer could be risky if one is overriding PHPUnit's native methods.

Configuration
-------------

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'3.0'``, ``'3.5'``, ``'5.0'``, ``'5.6'`` and ``'newest'``

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

- `@PHPUnit3.0Migration:risky <./../../ruleSets/PHPUnit3.0MigrationRisky.rst>`_ with config:

  ``['target' => '3.0']``

- `@PHPUnit3.2Migration:risky <./../../ruleSets/PHPUnit3.2MigrationRisky.rst>`_ with config:

  ``['target' => '3.0']``

- `@PHPUnit3.5Migration:risky <./../../ruleSets/PHPUnit3.5MigrationRisky.rst>`_ with config:

  ``['target' => '3.5']``

- `@PHPUnit4.3Migration:risky <./../../ruleSets/PHPUnit4.3MigrationRisky.rst>`_ with config:

  ``['target' => '3.5']``

- `@PHPUnit4.8Migration:risky <./../../ruleSets/PHPUnit4.8MigrationRisky.rst>`_ with config:

  ``['target' => '3.5']``

- `@PHPUnit5.0Migration:risky <./../../ruleSets/PHPUnit5.0MigrationRisky.rst>`_ with config:

  ``['target' => '5.0']``

- `@PHPUnit5.2Migration:risky <./../../ruleSets/PHPUnit5.2MigrationRisky.rst>`_ with config:

  ``['target' => '5.0']``

- `@PHPUnit5.4Migration:risky <./../../ruleSets/PHPUnit5.4MigrationRisky.rst>`_ with config:

  ``['target' => '5.0']``

- `@PHPUnit5.5Migration:risky <./../../ruleSets/PHPUnit5.5MigrationRisky.rst>`_ with config:

  ``['target' => '5.0']``

- `@PHPUnit5.6Migration:risky <./../../ruleSets/PHPUnit5.6MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit5.7Migration:risky <./../../ruleSets/PHPUnit5.7MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit6.0Migration:risky <./../../ruleSets/PHPUnit6.0MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit7.5Migration:risky <./../../ruleSets/PHPUnit7.5MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit8.4Migration:risky <./../../ruleSets/PHPUnit8.4MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit9.1Migration:risky <./../../ruleSets/PHPUnit9.1MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit10.0Migration:risky <./../../ruleSets/PHPUnit10.0MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit30Migration:risky <./../../ruleSets/PHPUnit30MigrationRisky.rst>`_ with config:

  ``['target' => '3.0']``

- `@PHPUnit32Migration:risky <./../../ruleSets/PHPUnit32MigrationRisky.rst>`_ with config:

  ``['target' => '3.0']``

- `@PHPUnit35Migration:risky <./../../ruleSets/PHPUnit35MigrationRisky.rst>`_ with config:

  ``['target' => '3.5']``

- `@PHPUnit43Migration:risky <./../../ruleSets/PHPUnit43MigrationRisky.rst>`_ with config:

  ``['target' => '3.5']``

- `@PHPUnit48Migration:risky <./../../ruleSets/PHPUnit48MigrationRisky.rst>`_ with config:

  ``['target' => '3.5']``

- `@PHPUnit50Migration:risky <./../../ruleSets/PHPUnit50MigrationRisky.rst>`_ with config:

  ``['target' => '5.0']``

- `@PHPUnit52Migration:risky <./../../ruleSets/PHPUnit52MigrationRisky.rst>`_ with config:

  ``['target' => '5.0']``

- `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ with config:

  ``['target' => '5.0']``

- `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ with config:

  ``['target' => '5.0']``

- `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit91Migration:risky <./../../ruleSets/PHPUnit91MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ with config:

  ``['target' => '5.6']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDedicateAssertFixer <./../../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitDedicateAssertFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitDedicateAssertFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
