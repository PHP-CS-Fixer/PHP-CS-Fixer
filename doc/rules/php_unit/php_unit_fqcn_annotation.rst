=================================
Rule ``php_unit_fqcn_annotation``
=================================

PHPUnit annotations should be a FQCNs including a root namespace.

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
        /**
   -     * @expectedException InvalidArgumentException
   -     * @covers Project\NameSpace\Something
   -     * @coversDefaultClass Project\Default
   -     * @uses Project\Test\Util
   +     * @expectedException \InvalidArgumentException
   +     * @covers \Project\NameSpace\Something
   +     * @coversDefaultClass \Project\Default
   +     * @uses \Project\Test\Util
         */
        public function testSomeTest()
        {
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitFqcnAnnotationFixer <./../src/Fixer/PhpUnit/PhpUnitFqcnAnnotationFixer.php>`_
