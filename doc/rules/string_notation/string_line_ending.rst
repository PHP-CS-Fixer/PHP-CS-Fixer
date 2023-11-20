===========================
Rule ``string_line_ending``
===========================

All multi-line strings must use correct line ending.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

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

Source class
------------

`PhpCsFixer\\Fixer\\StringNotation\\StringLineEndingFixer <./../src/Fixer/StringNotation/StringLineEndingFixer.php>`_
