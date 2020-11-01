=========================================
Rule ``no_trailing_whitespace_in_string``
=========================================

There must be no trailing whitespace in strings.

.. warning:: Using this rule is risky.

   Changing the whitespaces in strings might affect string comparisons and
   outputs.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
   -<?php $a = '  
   -    foo 
   +<?php $a = '
   +    foo
    ';

Rule sets
---------

The rule is part of the following rule sets:

@Symfony:risky
  Using the ``@Symfony:risky`` rule set will enable the ``no_trailing_whitespace_in_string`` rule.

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``no_trailing_whitespace_in_string`` rule.
