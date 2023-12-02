====================================
Rule ``class_reference_name_casing``
====================================

When referencing an internal class it must be written using the correct casing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -throw new \exception();
   +throw new \Exception();

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Casing\\ClassReferenceNameCasingFixer <./../../../src/Fixer/Casing/ClassReferenceNameCasingFixer.php>`_
