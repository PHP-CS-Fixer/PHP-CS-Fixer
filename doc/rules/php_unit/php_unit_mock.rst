======================
Rule ``php_unit_mock``
======================

Usages of ``->getMock`` and ``->getMockWithoutInvokingTheOriginalConstructor``
methods MUST be replaced by ``->createMock`` or ``->createPartialMock`` methods.

.. warning:: Using this rule is risky.

   Risky when PHPUnit classes are overridden or not accessible, or when project
   has PHPUnit incompatibilities.

Configuration
-------------

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'5.4'``, ``'5.5'``, ``'newest'``

Default value: ``'newest'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,9 +3,9 @@
    {
        public function testFoo()
        {
   -        $mock = $this->getMockWithoutInvokingTheOriginalConstructor("Foo");
   -        $mock1 = $this->getMock("Foo");
   -        $mock1 = $this->getMock("Bar", ["aaa"]);
   +        $mock = $this->createMock("Foo");
   +        $mock1 = $this->createMock("Foo");
   +        $mock1 = $this->createPartialMock("Bar", ["aaa"]);
            $mock1 = $this->getMock("Baz", ["aaa"], ["argument"]); // version with more than 2 params is not supported
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['target' => '5.4']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,7 +3,7 @@
    {
        public function testFoo()
        {
   -        $mock1 = $this->getMock("Foo");
   +        $mock1 = $this->createMock("Foo");
            $mock1 = $this->getMock("Bar", ["aaa"]); // version with multiple params is not supported
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PHPUnit54Migration:risky
  Using the `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ rule set will enable the ``php_unit_mock`` rule with the config below:

  ``['target' => '5.4']``

@PHPUnit55Migration:risky
  Using the `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ rule set will enable the ``php_unit_mock`` rule with the config below:

  ``['target' => '5.5']``

@PHPUnit56Migration:risky
  Using the `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ rule set will enable the ``php_unit_mock`` rule with the config below:

  ``['target' => '5.5']``

@PHPUnit57Migration:risky
  Using the `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ rule set will enable the ``php_unit_mock`` rule with the config below:

  ``['target' => '5.5']``

@PHPUnit60Migration:risky
  Using the `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ rule set will enable the ``php_unit_mock`` rule with the config below:

  ``['target' => '5.5']``

@PHPUnit75Migration:risky
  Using the `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ rule set will enable the ``php_unit_mock`` rule with the config below:

  ``['target' => '5.5']``
