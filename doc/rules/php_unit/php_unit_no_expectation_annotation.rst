===========================================
Rule ``php_unit_no_expectation_annotation``
===========================================

Usages of ``@expectedException*`` annotations MUST be replaced by
``->setExpectedException*`` methods.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when PHPUnit classes are overridden or not accessible, or when project has
PHPUnit incompatibilities.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``target``,
``use_class_const``.

Configuration
-------------

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'3.2'``, ``'4.3'`` and ``'newest'``

Default value: ``'newest'``

``use_class_const``
~~~~~~~~~~~~~~~~~~~

Use ::class notation.

Allowed types: ``bool``

Default value: ``true``

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
        /**
   -     * @expectedException FooException
   -     * @expectedExceptionMessageRegExp /foo.*$/
   -     * @expectedExceptionCode 123
         */
        function testAaa()
        {
   +        $this->setExpectedExceptionRegExp(\FooException::class, '/foo.*$/', 123);
   +
            aaa();
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['target' => '3.2']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
   -     * @expectedException FooException
   -     * @expectedExceptionCode 123
         */
        function testBbb()
        {
   +        $this->setExpectedException(\FooException::class, null, 123);
   +
            bbb();
        }

        /**
         * @expectedException FooException
         * @expectedExceptionMessageRegExp /foo.*$/
         */
        function testCcc()
        {
            ccc();
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHPUnit3x2Migration:risky <./../../ruleSets/PHPUnit3x2MigrationRisky.rst>`_ with config:

  ``['target' => '3.2']``

- `@PHPUnit3x5Migration:risky <./../../ruleSets/PHPUnit3x5MigrationRisky.rst>`_ with config:

  ``['target' => '3.2']``

- `@PHPUnit4x3Migration:risky <./../../ruleSets/PHPUnit4x3MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit4x8Migration:risky <./../../ruleSets/PHPUnit4x8MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit5x0Migration:risky <./../../ruleSets/PHPUnit5x0MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit5x2Migration:risky <./../../ruleSets/PHPUnit5x2MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit5x4Migration:risky <./../../ruleSets/PHPUnit5x4MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit5x5Migration:risky <./../../ruleSets/PHPUnit5x5MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit5x6Migration:risky <./../../ruleSets/PHPUnit5x6MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit5x7Migration:risky <./../../ruleSets/PHPUnit5x7MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit6x0Migration:risky <./../../ruleSets/PHPUnit6x0MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit7x5Migration:risky <./../../ruleSets/PHPUnit7x5MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit8x4Migration:risky <./../../ruleSets/PHPUnit8x4MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit9x1Migration:risky <./../../ruleSets/PHPUnit9x1MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit10x0Migration:risky <./../../ruleSets/PHPUnit10x0MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit32Migration:risky <./../../ruleSets/PHPUnit32MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '3.2']``

- `@PHPUnit35Migration:risky <./../../ruleSets/PHPUnit35MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '3.2']``

- `@PHPUnit43Migration:risky <./../../ruleSets/PHPUnit43MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit48Migration:risky <./../../ruleSets/PHPUnit48MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit50Migration:risky <./../../ruleSets/PHPUnit50MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit52Migration:risky <./../../ruleSets/PHPUnit52MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit91Migration:risky <./../../ruleSets/PHPUnit91MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.3']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitNoExpectationAnnotationFixer <./../../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitNoExpectationAnnotationFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
