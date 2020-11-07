================================
Rule ``non_printable_character``
================================

Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible
unicode symbols.

.. warning:: Using this rule is risky.

   Risky when strings contain intended invisible characters.

Configuration
-------------

``use_escape_sequences_in_strings``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether characters should be replaced with escape sequences in strings.

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
   @@ -1 +1 @@
   -<?php echo "​Hello World !";
   +<?php echo "Hello World !";

Example #2
~~~~~~~~~~

With configuration: ``['use_escape_sequences_in_strings' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php echo "​Hello World !";
   +<?php echo "\u{200b}Hello\u{2007}World\u{a0}!";

Rule sets
---------

The rule is part of the following rule sets:

@PHP70Migration:risky
  Using the `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_ rule set will enable the ``non_printable_character`` rule with the config below:

  ``['use_escape_sequences_in_strings' => true]``

@PHP71Migration:risky
  Using the `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ rule set will enable the ``non_printable_character`` rule with the config below:

  ``['use_escape_sequences_in_strings' => true]``

@PHP80Migration:risky
  Using the `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ rule set will enable the ``non_printable_character`` rule with the config below:

  ``['use_escape_sequences_in_strings' => true]``

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``non_printable_character`` rule with the default config.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``non_printable_character`` rule with the default config.
