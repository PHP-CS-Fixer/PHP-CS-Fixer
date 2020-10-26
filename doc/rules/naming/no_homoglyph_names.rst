===========================
Rule ``no_homoglyph_names``
===========================

Replace accidental usage of homoglyphs (non ascii characters) in names.

.. warning:: Using this rule is risky.

   Renames classes and cannot rename the files. You might have string references
   to renamed code (``$$name``).

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $nаmе = 'wrong "a" character';
   +<?php $name = 'wrong "a" character';

Rule sets
---------

The rule is part of the following rule sets:

@Symfony:risky
  Using the ``@Symfony:risky`` rule set will enable the ``no_homoglyph_names`` rule.

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``no_homoglyph_names`` rule.
