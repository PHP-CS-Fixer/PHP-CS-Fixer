=======================================
Rule ``phpdoc_single_line_var_spacing``
=======================================

Single line ``@var`` PHPDoc should have proper spacing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
   -<?php /**@var   MyClass   $a   */
   +<?php /** @var MyClass $a */
    $a = test();

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_single_line_var_spacing`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_single_line_var_spacing`` rule.
