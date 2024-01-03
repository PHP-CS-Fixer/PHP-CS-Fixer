=====================
Rule ``group_import``
=====================

There MUST be group use for the same namespaces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -use Foo\Bar;
   -use Foo\Baz;
   +use Foo\{Bar, Baz};
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\GroupImportFixer <./../../../src/Fixer/Import/GroupImportFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\GroupImportFixerTest <./../../../tests/Fixer/Import/GroupImportFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
