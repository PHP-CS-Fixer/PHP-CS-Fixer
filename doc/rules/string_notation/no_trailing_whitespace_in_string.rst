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

@PER:risky
  Using the `@PER:risky <./../../ruleSets/PERRisky.rst>`_ rule set will enable the ``no_trailing_whitespace_in_string`` rule.

@PSR12:risky
  Using the `@PSR12:risky <./../../ruleSets/PSR12Risky.rst>`_ rule set will enable the ``no_trailing_whitespace_in_string`` rule.

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``no_trailing_whitespace_in_string`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``no_trailing_whitespace_in_string`` rule.
