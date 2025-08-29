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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Casing\\ClassReferenceNameCasingFixer <./../../../src/Fixer/Casing/ClassReferenceNameCasingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Casing\\ClassReferenceNameCasingFixerTest <./../../../tests/Fixer/Casing/ClassReferenceNameCasingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
