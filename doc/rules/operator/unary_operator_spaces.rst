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

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

