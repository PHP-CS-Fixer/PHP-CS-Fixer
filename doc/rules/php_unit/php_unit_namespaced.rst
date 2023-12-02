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

- `@PHPUnit48Migration:risky <./../../ruleSets/PHPUnit48MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit50Migration:risky <./../../ruleSets/PHPUnit50MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit52Migration:risky <./../../ruleSets/PHPUnit52MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit54Migration:risky <./../../ruleSets/PHPUnit54MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit55Migration:risky <./../../ruleSets/PHPUnit55MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit56Migration:risky <./../../ruleSets/PHPUnit56MigrationRisky.rst>`_ with config:

  ``['target' => '4.8']``

- `@PHPUnit57Migration:risky <./../../ruleSets/PHPUnit57MigrationRisky.rst>`_ with config:

  ``['target' => '5.7']``

- `@PHPUnit60Migration:risky <./../../ruleSets/PHPUnit60MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``

- `@PHPUnit75Migration:risky <./../../ruleSets/PHPUnit75MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``

- `@PHPUnit84Migration:risky <./../../ruleSets/PHPUnit84MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``

- `@PHPUnit100Migration:risky <./../../ruleSets/PHPUnit100MigrationRisky.rst>`_ with config:

  ``['target' => '6.0']``


Source class
------------

`PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitNamespacedFixer <./../../../src/Fixer/PhpUnit/PhpUnitNamespacedFixer.php>`_
