====================================
Rule ``semicolon_after_instruction``
====================================

Instructions must be terminated with a semicolon.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php echo 1 ?>
   +<?php echo 1; ?>

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Semicolon\\SemicolonAfterInstructionFixer <./../../../src/Fixer/Semicolon/SemicolonAfterInstructionFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Semicolon\\SemicolonAfterInstructionFixerTest <./../../../tests/Fixer/Semicolon/SemicolonAfterInstructionFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
