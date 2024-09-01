==================================
Rule ``php_unit_assert_new_names``
==================================

Rename deprecated PHPUnit assertions like ``assertFileNotExists`` to new methods
like ``assertFileDoesNotExist``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Fixer could be risky if one is overriding PHPUnit's native methods.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testSomeTest()
        {
   -        $this->assertFileNotExists("test.php");
   -        $this->assertNotIsWritable("path.php");
   +        $this->assertFileDoesNotExist("test.php");
   +        $this->assertIsNotWritable("path.php");
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHPUnit91Migration:risky <./../../ruleSets/PHPUnit91MigrationRisky.rst>`_
- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitAssertNewNamesFixer <./../../../src/Fixer/PhpUnit/PhpUnitAssertNewNamesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitAssertNewNamesFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitAssertNewNamesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
