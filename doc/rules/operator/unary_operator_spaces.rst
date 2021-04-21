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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``unary_operator_spaces`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``unary_operator_spaces`` rule.
