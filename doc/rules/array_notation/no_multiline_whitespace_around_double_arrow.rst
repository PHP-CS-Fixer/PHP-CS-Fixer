====================================================
Rule ``no_multiline_whitespace_around_double_arrow``
====================================================

Operator ``=>`` should not be surrounded by multi-line whitespaces.

Warning
-------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``no_line_break_near_binary_operator`` instead.

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\NoMultilineWhitespaceAroundDoubleArrowFixer <./../../../src/Fixer/ArrayNotation/NoMultilineWhitespaceAroundDoubleArrowFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\NoMultilineWhitespaceAroundDoubleArrowFixerTest <./../../../tests/Fixer/ArrayNotation/NoMultilineWhitespaceAroundDoubleArrowFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
