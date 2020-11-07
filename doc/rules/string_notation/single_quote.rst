=====================
Rule ``single_quote``
=====================

Convert double quotes to single quotes for simple strings.

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
   @@ -1,4 +1,4 @@
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
   @@ -1,4 +1,4 @@
    <?php

   -$a = "sample";
   -$b = "sample with 'single-quotes'";
   +$a = 'sample';
   +$b = 'sample with \'single-quotes\'';

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``single_quote`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``single_quote`` rule with the default config.
