===========================
Rule ``string_line_ending``
===========================

All multi-line strings must use correct line ending.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Changing the line endings of multi-line strings might affect string comparisons
and outputs.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = 'my^M
   +<?php $a = 'my
    multi
   -line^M
   +line
    string';^M

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\StringNotation\\StringLineEndingFixer <./../../../src/Fixer/StringNotation/StringLineEndingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\StringNotation\\StringLineEndingFixerTest <./../../../tests/Fixer/StringNotation/StringLineEndingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
