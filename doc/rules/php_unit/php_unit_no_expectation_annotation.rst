===========================================
Rule ``php_unit_no_expectation_annotation``
===========================================

Usages of ``@expectedException*`` annotations MUST be replaced by
``->setExpectedException*`` methods.

.. warning:: Using this rule is risky.

   Risky when PHPUnit classes are overridden or not accessible, or when project
   has PHPUnit incompatibilities.

Configuration
-------------

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'3.2'``, ``'4.3'``, ``'newest'``

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
   @@ -2,12 +2,11 @@
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
   @@ -2,11 +2,11 @@
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

Rule sets
---------

The rule is part of the following rule sets:

@PHPUnit32Migration:risky
  Using the ``@PHPUnit32Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '3.2']``

@PHPUnit35Migration:risky
  Using the ``@PHPUnit35Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '3.2']``

@PHPUnit43Migration:risky
  Using the ``@PHPUnit43Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``

@PHPUnit48Migration:risky
  Using the ``@PHPUnit48Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``

@PHPUnit50Migration:risky
  Using the ``@PHPUnit50Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``

@PHPUnit52Migration:risky
  Using the ``@PHPUnit52Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``

@PHPUnit54Migration:risky
  Using the ``@PHPUnit54Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``

@PHPUnit55Migration:risky
  Using the ``@PHPUnit55Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``

@PHPUnit56Migration:risky
  Using the ``@PHPUnit56Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``

@PHPUnit57Migration:risky
  Using the ``@PHPUnit57Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``

@PHPUnit60Migration:risky
  Using the ``@PHPUnit60Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``

@PHPUnit75Migration:risky
  Using the ``@PHPUnit75Migration:risky`` rule set will enable the ``php_unit_no_expectation_annotation`` rule with the config below:

  ``['target' => '4.3']``
