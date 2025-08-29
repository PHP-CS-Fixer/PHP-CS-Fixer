===============================
Rule ``lambda_not_used_import``
===============================

Lambda must not import variables it doesn't use.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = function() use ($bar) {};
   +$foo = function() {};

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\LambdaNotUsedImportFixer <./../../../src/Fixer/FunctionNotation/LambdaNotUsedImportFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\LambdaNotUsedImportFixerTest <./../../../tests/Fixer/FunctionNotation/LambdaNotUsedImportFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
