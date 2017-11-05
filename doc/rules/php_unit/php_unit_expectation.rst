=============================
Rule ``php_unit_expectation``
=============================

Usages of ``->setExpectedException*`` methods MUST be replaced by
``->expectException*`` methods.

.. warning:: Using this rule is risky.

   Risky when PHPUnit classes are overridden or not accessible, or when project
   has PHPUnit incompatibilities.

Configuration
-------------

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'5.2'``, ``'5.6'``, ``'newest'``

Default value: ``'newest'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,13 +3,17 @@
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
   +        $this->expectExceptionMessageRegExp("/Msg.*/");
   +        $this->expectExceptionCode(123);
            bar();
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['target' => '5.6']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,13 +3,16 @@
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

Example #3
~~~~~~~~~~

With configuration: ``['target' => '5.2']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,7 +3,9 @@
    {
        public function testFoo()
        {
   -        $this->setExpectedException("RuntimeException", "Msg", 123);
   +        $this->expectException("RuntimeException");
   +        $this->expectExceptionMessage("Msg");
   +        $this->expectExceptionCode(123);
            foo();
        }

Rule sets
---------

The rule is part of the following rule sets:

@PHPUnit52Migration:risky
  Using the ``@PHPUnit52Migration:risky`` rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.2']``

@PHPUnit54Migration:risky
  Using the ``@PHPUnit54Migration:risky`` rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.2']``

@PHPUnit55Migration:risky
  Using the ``@PHPUnit55Migration:risky`` rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.2']``

@PHPUnit56Migration:risky
  Using the ``@PHPUnit56Migration:risky`` rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit57Migration:risky
  Using the ``@PHPUnit57Migration:risky`` rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit60Migration:risky
  Using the ``@PHPUnit60Migration:risky`` rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.6']``

@PHPUnit75Migration:risky
  Using the ``@PHPUnit75Migration:risky`` rule set will enable the ``php_unit_expectation`` rule with the config below:

  ``['target' => '5.6']``
