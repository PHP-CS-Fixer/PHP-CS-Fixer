===================================================
Rule ``no_singleline_whitespace_before_semicolons``
===================================================

Single-line whitespace before closing semicolon are prohibited.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $this->foo() ;
   +<?php $this->foo();

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Semicolon\\NoSinglelineWhitespaceBeforeSemicolonsFixer <./../../../src/Fixer/Semicolon/NoSinglelineWhitespaceBeforeSemicolonsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Semicolon\\NoSinglelineWhitespaceBeforeSemicolonsFixerTest <./../../../tests/Fixer/Semicolon/NoSinglelineWhitespaceBeforeSemicolonsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
