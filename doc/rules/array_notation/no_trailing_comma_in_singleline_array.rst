==============================================
Rule ``no_trailing_comma_in_singleline_array``
==============================================

PHP single-line arrays should not have trailing comma.

Warning
-------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``no_trailing_comma_in_singleline`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = array('sample',  );
   +$a = array('sample');

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\NoTrailingCommaInSinglelineArrayFixer <./../../../src/Fixer/ArrayNotation/NoTrailingCommaInSinglelineArrayFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\NoTrailingCommaInSinglelineArrayFixerTest <./../../../tests/Fixer/ArrayNotation/NoTrailingCommaInSinglelineArrayFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
