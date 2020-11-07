================================
Rule ``ternary_operator_spaces``
================================

Standardize spaces around ternary operator.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $a = $a   ?1 :0;
   +<?php $a = $a ? 1 : 0;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``ternary_operator_spaces`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``ternary_operator_spaces`` rule.
