=======================================
Rule ``no_trailing_comma_in_list_call``
=======================================

Remove trailing commas in list function calls.

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
   -list($a, $b,) = foo();
   +list($a, $b) = foo();

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\NoTrailingCommaInListCallFixer <./../../../src/Fixer/ControlStructure/NoTrailingCommaInListCallFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\NoTrailingCommaInListCallFixerTest <./../../../tests/Fixer/ControlStructure/NoTrailingCommaInListCallFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
