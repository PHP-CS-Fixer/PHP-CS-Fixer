=========================================
Rule ``no_alias_language_construct_call``
=========================================

Master language constructs shall be used instead of aliases.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -die;
   +exit;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_alias_language_construct_call`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_alias_language_construct_call`` rule.
