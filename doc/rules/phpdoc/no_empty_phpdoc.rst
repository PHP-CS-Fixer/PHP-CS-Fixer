========================
Rule ``no_empty_phpdoc``
========================

There should not be empty PHPDoc blocks.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php /**  */
   +<?php 

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_empty_phpdoc`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_empty_phpdoc`` rule.
