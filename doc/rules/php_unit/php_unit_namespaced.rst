============================
Rule ``php_unit_namespaced``
============================

PHPUnit classes MUST be used in namespaced version, e.g.
``\PHPUnit\Framework\TestCase`` instead of ``\PHPUnit_Framework_TestCase``.

Description
-----------

PHPUnit v6 has finally fully switched to namespaces.
You could start preparing the upgrade by switching from non-namespaced TestCase
to namespaced one.
Forward compatibility layer (``\PHPUnit\Framework\TestCase`` class) was
backported to PHPUnit v4.8.35 and PHPUnit v5.4.0.
Extended forward compatibility layer (``PHPUnit\Framework\Assert``,
``PHPUnit\Framework\BaseTestListener``, ``PHPUnit\Framework\TestListener``
classes) was introduced in v5.7.0.


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

Allowed values: ``'4.8'``, ``'5.7'``, ``'6.0'`` and ``'newest'``

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
   -final class MyTest extends \PHPUnit_Framework_TestCase
   +final class MyTest extends \PHPUnit\Framework\TestCase
    {
        public function testSomething()
        {
   -        PHPUnit_Framework_Assert::assertTrue(true);
   +        PHPUnit\Framework\Assert::assertTrue(true);
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['target' => '4.8']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -final class MyTest extends \PHPUnit_Framework_TestCase
   +final class MyTest extends \PHPUnit\Framework\TestCase
    {
        public function testSomething()
        {
            PHPUnit_Framework_Assert::assertTrue(true);
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PHPUnit4x8Migration:risky <./../../ruleSets/PHPUnit4x8MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit5x0Migration:risky <./../../ruleSets/PHPUnit5x0MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit5x2Migration:risky <./../../ruleSets/PHPUnit5x2MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit5x4Migration:risky <./../../ruleSets/PHPUnit5x4MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit5x5Migration:risky <./../../ruleSets/PHPUnit5x5MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit5x6Migration:risky <./../../ruleSets/PHPUnit5x6MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit5x7Migration:risky <./../../ruleSets/PHPUnit5x7MigrationRisky.rst>`_ with config:

  ``['target' => '5.7']``

- `@PHPUnit6x0Migration:risky <./../../ruleSets/PHPUnit6x0MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``

- `@PHPUnit7x5Migration:risky <./../../ruleSets/PHPUnit7x5MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``

- `@PHPUnit8x4Migration:risky <./../../ruleSets/PHPUnit8x4MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``

- `@PHPUnit9x1Migration:risky <./../../ruleSets/PHPUnit9x1MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``

- `@PHPUnit10x0Migration:risky <./../../ruleSets/PHPUnit10x0MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``

- `@PHPUnit11x0Migration:risky <./../../ruleSets/PHPUnit11x0MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``

- `@PHPUnit48Migration:risky <./../../ruleSets/PHPUnit48MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.8']``

- `@PHPUnit50Migration:risky <./../../ruleSets/PHPUnit50MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.8']``

- `@PHPUnit52Migration:risky <./../../ruleSets/PHPUnit52MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.8']``

- `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.8']``

- `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.8']``

- `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '4.8']``

- `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '5.7']``

- `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '6.0']``

- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '6.0']``

- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '6.0']``

- `@PHPUnit91Migration:risky <./../../ruleSets/PHPUnit91MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '6.0']``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ *(deprecated)* with config:

  ``['target' => '6.0']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitNamespacedFixer <./../../../src/Fixer/PhpUnit/PhpUnitNamespacedFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitNamespacedFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitNamespacedFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
