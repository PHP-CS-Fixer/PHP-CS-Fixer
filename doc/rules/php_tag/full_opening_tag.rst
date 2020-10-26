=========================
Rule ``full_opening_tag``
=========================

PHP code must use the long ``<?php`` tags or short-echo ``<?=`` tags and not
other tag variations.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
   -<?
   +<?php

    echo "Hello!";

Rule sets
---------

The rule is part of the following rule sets:

@PSR1
  Using the ``@PSR1`` rule set will enable the ``full_opening_tag`` rule.

@PSR2
  Using the ``@PSR2`` rule set will enable the ``full_opening_tag`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``full_opening_tag`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``full_opening_tag`` rule.
