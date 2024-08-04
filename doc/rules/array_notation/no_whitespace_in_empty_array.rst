=====================================
Rule ``no_whitespace_in_empty_array``
=====================================

Empty arrays should not contain any whitespace.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$foo = [
   -];
   +$foo = [];
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\NoWhitespaceInEmptyArrayFixer <./../../../src/Fixer/ArrayNotation/NoWhitespaceInEmptyArrayFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\NoWhitespaceInEmptyArrayFixerTest <./../../../tests/Fixer/ArrayNotation/NoWhitespaceInEmptyArrayFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
