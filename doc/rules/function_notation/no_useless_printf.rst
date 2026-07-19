==========================
Rule ``no_useless_printf``
==========================

There must be no ``printf`` calls with only the first argument.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when the ``printf`` function is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -printf('bar');
   +print 'bar';

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\NoUselessPrintfFixer <./../../../src/Fixer/FunctionNotation/NoUselessPrintfFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\NoUselessPrintfFixerTest <./../../../tests/Fixer/FunctionNotation/NoUselessPrintfFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
