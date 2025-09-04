================================
Rule ``no_leading_import_slash``
================================

Remove leading slashes in ``use`` clauses.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    namespace Foo;
   -use \Bar;
   +use Bar;

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\NoLeadingImportSlashFixer <./../../../src/Fixer/Import/NoLeadingImportSlashFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\NoLeadingImportSlashFixerTest <./../../../tests/Fixer/Import/NoLeadingImportSlashFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
