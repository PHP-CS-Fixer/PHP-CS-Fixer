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
Source class
------------

`PhpCsFixer\\Fixer\\Import\\GroupImportFixer <./../../../src/Fixer/Import/GroupImportFixer.php>`_
