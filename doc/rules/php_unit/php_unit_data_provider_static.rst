======================================
Rule ``php_unit_data_provider_static``
======================================

Data providers must be static.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Fixer could be risky if one is calling data provider function dynamically.

Configuration
-------------

``force``
~~~~~~~~~

Whether to make the data providers static even if they have a dynamic class call
(may introduce fatal error "using $this when not in object context", and you may
have to adjust the code manually by converting dynamic calls to static ones).

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class FooTest extends TestCase {
        /**
         * @dataProvider provideSomethingCases
         */
        public function testSomething($expected, $actual) {}
   -    public function provideSomethingCases() {}
   +    public static function provideSomethingCases() {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['force' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class FooTest extends TestCase {
        /**
         * @dataProvider provideSomethingCases1
         * @dataProvider provideSomethingCases2
         */
        public function testSomething($expected, $actual) {}
   -    public function provideSomethingCases1() { $this->getData1(); }
   -    public function provideSomethingCases2() { self::getData2(); }
   +    public static function provideSomethingCases1() { $this->getData1(); }
   +    public static function provideSomethingCases2() { self::getData2(); }
    }

Example #3
~~~~~~~~~~

With configuration: ``['force' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class FooTest extends TestCase {
        /**
         * @dataProvider provideSomething1Cases
         * @dataProvider provideSomething2Cases
         */
        public function testSomething($expected, $actual) {}
        public function provideSomething1Cases() { $this->getData1(); }
   -    public function provideSomething2Cases() { self::getData2(); }
   +    public static function provideSomething2Cases() { self::getData2(); }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHPUnit10x0Migration:risky <./../../ruleSets/PHPUnit10x0MigrationRisky.rst>`_ with config:

  ``['force' => true]``

- `@PHPUnit11x0Migration:risky <./../../ruleSets/PHPUnit11x0MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['force' => true]``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['force' => true]``

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ with config:

  ``['force' => true]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDataProviderStaticFixer <./../../../src/Fixer/PhpUnit/PhpUnitDataProviderStaticFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitDataProviderStaticFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitDataProviderStaticFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
