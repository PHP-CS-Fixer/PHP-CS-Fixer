==============================
Rule ``unary_operator_spaces``
==============================

Unary operators should be placed adjacent to their operands.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,6 +1,6 @@
    <?php
   -$sample ++;
   --- $sample;
   -$sample = ! ! $a;
   -$sample = ~  $c;
   -function & foo(){}
   +$sample++;
   +--$sample;
   +$sample = !!$a;
   +$sample = ~$c;
   +function &foo(){}

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``unary_operator_spaces`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``unary_operator_spaces`` rule.
