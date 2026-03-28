====================================
Rule ``single_import_per_statement``
====================================

There MUST be one use keyword per declaration.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option:
``group_to_single_imports``.

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

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)* with config:

  ``['group_to_single_imports' => false]``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['group_to_single_imports' => false]``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ *(deprecated)* with config:

  ``['group_to_single_imports' => false]``

- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_ with config:

  ``['group_to_single_imports' => false]``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)* with config:

  ``['group_to_single_imports' => false]``

- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_ with config:

  ``['group_to_single_imports' => false]``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)* with config:

  ``['group_to_single_imports' => false]``

- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_ with config:

  ``['group_to_single_imports' => false]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['group_to_single_imports' => false]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\SingleImportPerStatementFixer <./../../../src/Fixer/Import/SingleImportPerStatementFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\SingleImportPerStatementFixerTest <./../../../tests/Fixer/Import/SingleImportPerStatementFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
