===========================
Rule ``no_useless_sprintf``
===========================

There must be no ``sprintf`` calls with only the first argument.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when if the ``sprintf`` function is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = sprintf('bar');
   +$foo = 'bar';

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\NoUselessSprintfFixer <./../../../src/Fixer/FunctionNotation/NoUselessSprintfFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\NoUselessSprintfFixerTest <./../../../tests/Fixer/FunctionNotation/NoUselessSprintfFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
