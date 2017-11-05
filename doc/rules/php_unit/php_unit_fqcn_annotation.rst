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
   @@ -2,12 +2,12 @@
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

@Symfony
  Using the ``@Symfony`` rule set will enable the ``php_unit_fqcn_annotation`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``php_unit_fqcn_annotation`` rule.
