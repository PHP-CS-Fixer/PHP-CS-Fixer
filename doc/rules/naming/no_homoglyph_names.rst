===========================
Rule ``no_homoglyph_names``
===========================

Replace accidental usage of homoglyphs (non ascii characters) in names.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Renames classes and cannot rename the files. You might have string references to
renamed code (``$$name``).

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $nаmе = 'wrong "a" character';
   +<?php $name = 'wrong "a" character';

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Naming\\NoHomoglyphNamesFixer <./../src/Fixer/Naming/NoHomoglyphNamesFixer.php>`_
