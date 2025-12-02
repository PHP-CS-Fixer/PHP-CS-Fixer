=====================
Rule ``single_quote``
=====================

Convert double quotes to single quotes for simple strings.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option:
``strings_containing_single_quote_chars``.

Configuration
-------------

``strings_containing_single_quote_chars``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to fix double-quoted strings that contains single-quotes.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$a = "sample";
   +$a = 'sample';
    $b = "sample with 'single-quotes'";

Example #2
~~~~~~~~~~

With configuration: ``['strings_containing_single_quote_chars' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$a = "sample";
   -$b = "sample with 'single-quotes'";
   +$a = 'sample';
   +$b = 'sample with \'single-quotes\'';

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\StringNotation\\SingleQuoteFixer <./../../../src/Fixer/StringNotation/SingleQuoteFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\StringNotation\\SingleQuoteFixerTest <./../../../tests/Fixer/StringNotation/SingleQuoteFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
