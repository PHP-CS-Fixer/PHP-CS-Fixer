======================================================
Rule ``no_trailing_comma_in_singleline_function_call``
======================================================

When making a method or function call on a single line there MUST NOT be a
trailing comma after the last argument.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``no_trailing_comma_in_singleline`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -foo($a,);
   +foo($a);

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\NoTrailingCommaInSinglelineFunctionCallFixer <./../../../src/Fixer/FunctionNotation/NoTrailingCommaInSinglelineFunctionCallFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\NoTrailingCommaInSinglelineFunctionCallFixerTest <./../../../tests/Fixer/FunctionNotation/NoTrailingCommaInSinglelineFunctionCallFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
