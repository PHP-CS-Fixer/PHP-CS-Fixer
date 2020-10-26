===================================================
Rule ``no_singleline_whitespace_before_semicolons``
===================================================

Single-line whitespace before closing semicolon are prohibited.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $this->foo() ;
   +<?php $this->foo();

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_singleline_whitespace_before_semicolons`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_singleline_whitespace_before_semicolons`` rule.
