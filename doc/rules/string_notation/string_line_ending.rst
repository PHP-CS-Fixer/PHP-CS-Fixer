===========================
Rule ``string_line_ending``
===========================

All multi-line strings must use correct line ending.

.. warning:: Using this rule is risky.

   Changing the line endings of multi-line strings might affect string
   comparisons and outputs.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
   -<?php $a = 'my^M
   +<?php $a = 'my
    multi
   -line^M
   +line
    string';^M

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``string_line_ending`` rule.
