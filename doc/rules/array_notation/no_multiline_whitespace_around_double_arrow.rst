====================================================
Rule ``no_multiline_whitespace_around_double_arrow``
====================================================

Operator ``=>`` should not be surrounded by multi-line whitespaces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = array(1
   -
   -=> 2);
   +$a = array(1 => 2);

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\NoMultilineWhitespaceAroundDoubleArrowFixer <./../../../src/Fixer/ArrayNotation/NoMultilineWhitespaceAroundDoubleArrowFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\NoMultilineWhitespaceAroundDoubleArrowFixerTest <./../../../tests/Fixer/ArrayNotation/NoMultilineWhitespaceAroundDoubleArrowFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
