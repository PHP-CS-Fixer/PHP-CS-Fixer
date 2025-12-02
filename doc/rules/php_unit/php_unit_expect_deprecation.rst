====================================
Rule ``php_unit_expect_deprecation``
====================================

Usages of ``@expectedDeprecation`` annotations MUST be replaced by
``expectDeprecation()`` method calls.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when PHPUnit classes are overridden or not accessible, or when project has
PHPUnit incompatibilities.

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
   -     * @expectedDeprecation Deprecation message
         */
        public function testAaa()
        {
   +        $this->expectDeprecation('Deprecation message');
   +
            aaa();
        }
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitExpectDeprecationFixer <./../../../src/Fixer/PhpUnit/PhpUnitExpectDeprecationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitExpectDeprecationFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitExpectDeprecationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
