========================
Rule ``new_with_braces``
========================

All instances created with new keyword must be followed by braces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $x = new X;
   +<?php $x = new X();

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``new_with_braces`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``new_with_braces`` rule.
