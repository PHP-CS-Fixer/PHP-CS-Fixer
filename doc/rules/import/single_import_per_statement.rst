====================================
Rule ``single_import_per_statement``
====================================

There MUST be one use keyword per declaration.

Configuration
-------------

``group_to_single_imports``
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to change group imports into single imports.

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
   -use Foo, Sample, Sample\Sample as Sample2;
   +use Foo;
   +use Sample;
   +use Sample\Sample as Sample2;

Example #2
~~~~~~~~~~

With configuration: ``['group_to_single_imports' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -use Space\Models\ {
   -    TestModelA,
   -    TestModelB,
   -    TestModel,
   -};
   +use Space\Models\TestModelA;
   +use Space\Models\TestModelB;
   +use Space\Models\TestModel;

Rule sets
---------

The rule is part of the following rule sets:

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``single_import_per_statement`` rule with the config below:

  ``['group_to_single_imports' => false]``

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``single_import_per_statement`` rule with the default config.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``single_import_per_statement`` rule with the config below:

  ``['group_to_single_imports' => false]``

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``single_import_per_statement`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``single_import_per_statement`` rule with the default config.
