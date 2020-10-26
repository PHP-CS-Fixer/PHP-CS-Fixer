=======================================
Rule ``switch_case_semicolon_to_colon``
=======================================

A case should be followed by a colon and not a semicolon.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,7 @@
    <?php
        switch ($a) {
   -        case 1;
   +        case 1:
                break;
   -        default;
   +        default:
                break;
        }

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``switch_case_semicolon_to_colon`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``switch_case_semicolon_to_colon`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``switch_case_semicolon_to_colon`` rule.
