============================================
Rule ``php_unit_test_class_requires_covers``
============================================

Adds a default ``@coversNothing`` annotation to PHPUnit test classes that have
no ``@covers*`` annotation.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +
   +/**
   + * @coversNothing
   + */
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testSomeTest()
        {
            $this->assertSame(a(), b());
        }
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitTestClassRequiresCoversFixer <./../../../src/Fixer/PhpUnit/PhpUnitTestClassRequiresCoversFixer.php>`_
