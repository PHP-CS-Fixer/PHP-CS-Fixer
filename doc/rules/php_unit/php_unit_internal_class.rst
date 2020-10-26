================================
Rule ``php_unit_internal_class``
================================

All PHPUnit test classes should be marked as internal.

Configuration
-------------

``types``
~~~~~~~~~

What types of classes to mark as internal

Allowed values: a subset of ``['normal', 'final', 'abstract']``

Default value: ``['normal', 'final']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,5 @@
    <?php
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
   @@ -1,4 +1,7 @@
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

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``php_unit_internal_class`` rule with the default config.
