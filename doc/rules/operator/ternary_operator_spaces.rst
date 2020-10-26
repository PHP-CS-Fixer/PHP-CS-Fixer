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

@Symfony
  Using the ``@Symfony`` rule set will enable the ``ternary_operator_spaces`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``ternary_operator_spaces`` rule.
