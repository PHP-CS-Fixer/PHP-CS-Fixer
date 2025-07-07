===========================================
Rule ``php_unit_no_expectation_annotation``
===========================================

Usages of ``@expectedException*`` annotations MUST be replaced by
``->setExpectedException*`` methods.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when PHPUnit classes are overridden or not accessible, or when project has
PHPUnit incompatibilities.

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

- `@PHPUnit32Migration:risky <./../../ruleSets/PHPUnit32MigrationRisky.rst>`_ with config:

  ``['target' => '3.2']``

- `@PHPUnit35Migration:risky <./../../ruleSets/PHPUnit35MigrationRisky.rst>`_ with config:

  ``['target' => '3.2']``

- `@PHPUnit43Migration:risky <./../../ruleSets/PHPUnit43MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit48Migration:risky <./../../ruleSets/PHPUnit48MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit50Migration:risky <./../../ruleSets/PHPUnit50MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit52Migration:risky <./../../ruleSets/PHPUnit52MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit91Migration:risky <./../../ruleSets/PHPUnit91MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ with config:

  ``['target' => '4.3']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitNoExpectationAnnotationFixer <./../../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitNoExpectationAnnotationFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
