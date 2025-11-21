================================
Rule ``non_printable_character``
================================

Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible
unicode symbols.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when strings contain intended invisible characters.

Configuration
-------------

``use_escape_sequences_in_strings``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether characters should be replaced with escape sequences in strings.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php echo "​Hello World !";
   +<?php echo "\u{200b}Hello\u{2007}World\u{a0}!";

Example #2
~~~~~~~~~~

With configuration: ``['use_escape_sequences_in_strings' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php echo "​Hello World !";
   +<?php echo "Hello World !";

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP7x0Migration:risky <./../../ruleSets/PHP7x0MigrationRisky.rst>`_
- `@PHP7x1Migration:risky <./../../ruleSets/PHP7x1MigrationRisky.rst>`_
- `@PHP7x4Migration:risky <./../../ruleSets/PHP7x4MigrationRisky.rst>`_
- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_
- `@PHP8x1Migration:risky <./../../ruleSets/PHP8x1MigrationRisky.rst>`_
- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP8x3Migration:risky <./../../ruleSets/PHP8x3MigrationRisky.rst>`_
- `@PHP8x4Migration:risky <./../../ruleSets/PHP8x4MigrationRisky.rst>`_
- `@PHP8x5Migration:risky <./../../ruleSets/PHP8x5MigrationRisky.rst>`_
- `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_ *(deprecated)*
- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ *(deprecated)*
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ *(deprecated)*
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ *(deprecated)*
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Basic\\NonPrintableCharacterFixer <./../../../src/Fixer/Basic/NonPrintableCharacterFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Basic\\NonPrintableCharacterFixerTest <./../../../tests/Fixer/Basic/NonPrintableCharacterFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
