================================
Rule ``php_unit_internal_class``
================================

All PHPUnit test classes should be marked as internal.

Configuration
-------------

``types``
~~~~~~~~~

What types of classes to mark as internal.

Allowed values: a subset of ``['abstract', 'final', 'normal']``

Default value: ``['normal', 'final']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +
   +/**
   + * @internal
   + */
    class MyTest extends TestCase {}

Example #2
~~~~~~~~~~

With configuration: ``['types' => ['final']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class MyTest extends TestCase {}
   +/**
   + * @internal
   + */
    final class FinalTest extends TestCase {}
    abstract class AbstractTest extends TestCase {}

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitInternalClassFixer <./../src/Fixer/PhpUnit/PhpUnitInternalClassFixer.php>`_
