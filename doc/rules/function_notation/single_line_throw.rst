==========================
Rule ``single_line_throw``
==========================

Throwing exception must be done in single line.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -throw new Exception(
   -    'Error.',
   -    500
   -);
   +throw new Exception('Error.', 500);

Rule sets
---------

The rule is part of the following rule set:

- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\SingleLineThrowFixer <./../../../src/Fixer/FunctionNotation/SingleLineThrowFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\SingleLineThrowFixerTest <./../../../tests/Fixer/FunctionNotation/SingleLineThrowFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
