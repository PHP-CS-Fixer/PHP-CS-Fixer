==========================
Rule ``switch_case_space``
==========================

Removes extra spaces between colon and case value.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,7 @@
    <?php
        switch($a) {
   -        case 1   :
   +        case 1:
                break;
   -        default     :
   +        default:
                return 2;
        }

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``switch_case_space`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``switch_case_space`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``switch_case_space`` rule.
