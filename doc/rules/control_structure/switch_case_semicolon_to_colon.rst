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

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``switch_case_semicolon_to_colon`` rule.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``switch_case_semicolon_to_colon`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``switch_case_semicolon_to_colon`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``switch_case_semicolon_to_colon`` rule.
