=================================
Rule ``no_unneeded_import_alias``
=================================

Imports should not be aliased as the same name.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -use A\B\Foo as Foo;
   +use A\B\Foo  ;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\NoUnneededImportAliasFixer <./../../../src/Fixer/Import/NoUnneededImportAliasFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\NoUnneededImportAliasFixerTest <./../../../tests/Fixer/Import/NoUnneededImportAliasFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
