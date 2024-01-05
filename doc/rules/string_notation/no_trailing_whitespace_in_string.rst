=========================================
Rule ``no_trailing_whitespace_in_string``
=========================================

There must be no trailing whitespace in strings.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Changing the whitespaces in strings might affect string comparisons and outputs.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = '  
   -    foo 
   +<?php $a = '
   +    foo
    ';

Rule sets
---------

The rule is part of the following rule sets:

- `@PER-CS1.0:risky <./../../ruleSets/PER-CS1.0Risky.rst>`_
- `@PER-CS2.0:risky <./../../ruleSets/PER-CS2.0Risky.rst>`_
- `@PER-CS:risky <./../../ruleSets/PER-CSRisky.rst>`_
- `@PER:risky <./../../ruleSets/PERRisky.rst>`_
- `@PSR12:risky <./../../ruleSets/PSR12Risky.rst>`_
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\StringNotation\\NoTrailingWhitespaceInStringFixer <./../../../src/Fixer/StringNotation/NoTrailingWhitespaceInStringFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\StringNotation\\NoTrailingWhitespaceInStringFixerTest <./../../../tests/Fixer/StringNotation/NoTrailingWhitespaceInStringFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
