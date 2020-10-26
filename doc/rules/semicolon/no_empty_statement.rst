===========================
Rule ``no_empty_statement``
===========================

Remove useless semicolon statements.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $a = 1;;
   +<?php $a = 1;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_empty_statement`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_empty_statement`` rule.
