====================================
Rule ``semicolon_after_instruction``
====================================

Instructions must be terminated with a semicolon.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php echo 1 ?>
   +<?php echo 1; ?>

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``semicolon_after_instruction`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``semicolon_after_instruction`` rule.
