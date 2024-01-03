===============================
Rule ``native_function_casing``
===============================

Function defined by PHP should be called using the correct casing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -STRLEN($str);
   +strlen($str);

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Casing\\NativeFunctionCasingFixer <./../../../src/Fixer/Casing/NativeFunctionCasingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Casing\\NativeFunctionCasingFixerTest <./../../../tests/Fixer/Casing/NativeFunctionCasingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
