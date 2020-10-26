===========================================
Rule ``single_blank_line_before_namespace``
===========================================

There should be exactly one blank line before a namespace declaration.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1,3 @@
   -<?php  namespace A {}
   +<?php
   +
   +namespace A {}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,3 @@
    <?php

   -
    namespace A{}

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``single_blank_line_before_namespace`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``single_blank_line_before_namespace`` rule.
