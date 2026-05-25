========================================================
Rule ``php_unit_does_not_perform_assertions_annotation``
========================================================

Use PHPUnit assertion ``expectNotToPerformAssertion`` instead of
``@doesNotPerformAssertions`` annotation.

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
   -     * @doesNotPerformAssertions
         */
        public function testFix(): void
        {
   +        $this->expectNotToPerformAssertions();
   +
            foo();
        }
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDoesNotPerformAssertionsAnnotationFixer <./../../../src/Fixer/PhpUnit/PhpUnitDoesNotPerformAssertionsAnnotationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitDoesNotPerformAssertionsAnnotationFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitDoesNotPerformAssertionsAnnotationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
