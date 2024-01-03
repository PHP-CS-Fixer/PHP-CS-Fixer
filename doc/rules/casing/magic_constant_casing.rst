==============================
Rule ``magic_constant_casing``
==============================

Magic constants should be referred to using the correct casing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -echo __dir__;
   +echo __DIR__;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Casing\\MagicConstantCasingFixer <./../../../src/Fixer/Casing/MagicConstantCasingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Casing\\MagicConstantCasingFixerTest <./../../../tests/Fixer/Casing/MagicConstantCasingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
