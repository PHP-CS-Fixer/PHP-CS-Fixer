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

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Operator\\UnaryOperatorSpacesFixer <./../../../src/Fixer/Operator/UnaryOperatorSpacesFixer.php>`_
