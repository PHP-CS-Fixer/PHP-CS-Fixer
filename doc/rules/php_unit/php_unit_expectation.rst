=============================
Rule ``php_unit_expectation``
=============================

Usages of ``->setExpectedException*`` methods MUST be replaced by
``->expectException*`` methods.

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

Allowed values: ``'5.2'``, ``'5.6'``, ``'8.4'``, ``'newest'``

Default value: ``'newest'``

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
        public function testFoo()
        {
   -        $this->setExpectedException("RuntimeException", "Msg", 123);
   +        $this->expectException("RuntimeException");
   +        $this->expectExceptionMessage("Msg");
   +        $this->expectExceptionCode(123);
            foo();
        }

        public function testBar()
        {
   -        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
   +        $this->expectException("RuntimeException");
   +        $this->expectExceptionMessageMatches("/Msg.*/");
   +        $this->expectExceptionCode(123);
            bar();
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['target' => '8.4']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
   -        $this->setExpectedException("RuntimeException", null, 123);
   +        $this->expectException("RuntimeException");
   +        $this->expectExceptionCode(123);
            foo();
        }

        public function testBar()
        {
   -        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
   +        $this->expectException("RuntimeException");
   +        $this->expectExceptionMessageMatches("/Msg.*/");
   +        $this->expectExceptionCode(123);
            bar();
        }
    }

Example #3
~~~~~~~~~~

With configuration: ``['target' => '5.6']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
   -        $this->setExpectedException("RuntimeException", null, 123);
   +        $this->expectException("RuntimeException");
   +        $this->expectExceptionCode(123);
            foo();
        }

        public function testBar()
        {
   -        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
   +        $this->expectException("RuntimeException");
   +        $this->expectExceptionMessageRegExp("/Msg.*/");
   +        $this->expectExceptionCode(123);
            bar();
        }
    }

Example #4
~~~~~~~~~~

With configuration: ``['target' => '5.2']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
   -        $this->setExpectedException("RuntimeException", "Msg", 123);
   +        $this->expectException("RuntimeException");
   +        $this->expectExceptionMessage("Msg");
   +        $this->expectExceptionCode(123);
            foo();
        }

        public function testBar()
        {
            $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
            bar();
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PHPUnit52Migration:risky
  Using the `@PHPUnit52Migration:risky <./../../ruleSets/PHPUnit52MigrationRisky.rst>`_ rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.2']``

@PHPUnit54Migration:risky
  Using the `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.2']``

@PHPUnit55Migration:risky
  Using the `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.2']``

@PHPUnit56Migration:risky
  Using the `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit57Migration:risky
  Using the `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit60Migration:risky
  Using the `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit75Migration:risky
  Using the `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit84Migration:risky
  Using the `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '8.4']``

@PHPUnit100Migration:risky
  Using the `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '8.4']``
