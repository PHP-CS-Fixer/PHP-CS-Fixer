========================================================
Rule ``php_unit_does_not_perform_assertions_annotation``
========================================================

Usages of the ``@doesNotPerformAssertions`` annotation MUST be replaced by the
``->expectNotToPerformAssertions()`` method.

Description
-----------

The method was added in PHPUnit 7.2.

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
    final class MyTest extends \PHPUnit\Framework\TestCase
    {
        /**
   -     * @doesNotPerformAssertions
         */
        public function testFoo(): void
        {
   +        $this->expectNotToPerformAssertions();
   +
            foo();
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHPUnit7x2Migration:risky <./../../ruleSets/PHPUnit7x2MigrationRisky.rst>`_
- `@PHPUnit7x5Migration:risky <./../../ruleSets/PHPUnit7x5MigrationRisky.rst>`_
- `@PHPUnit8x4Migration:risky <./../../ruleSets/PHPUnit8x4MigrationRisky.rst>`_
- `@PHPUnit9x1Migration:risky <./../../ruleSets/PHPUnit9x1MigrationRisky.rst>`_
- `@PHPUnit10x0Migration:risky <./../../ruleSets/PHPUnit10x0MigrationRisky.rst>`_
- `@PHPUnit11x0Migration:risky <./../../ruleSets/PHPUnit11x0MigrationRisky.rst>`_
- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ *(deprecated)*
- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ *(deprecated)*
- `@PHPUnit91Migration:risky <./../../ruleSets/PHPUnit91MigrationRisky.rst>`_ *(deprecated)*
- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ *(deprecated)*

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDoesNotPerformAssertionsAnnotationFixer <./../../../src/Fixer/PhpUnit/PhpUnitDoesNotPerformAssertionsAnnotationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitDoesNotPerformAssertionsAnnotationFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitDoesNotPerformAssertionsAnnotationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
