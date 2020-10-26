========================================
Rule ``whitespace_after_comma_in_array``
========================================

In array declaration, there MUST be a whitespace after each comma.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$sample = array(1,'a',$b,);
   +$sample = array(1, 'a', $b, );

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``whitespace_after_comma_in_array`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``whitespace_after_comma_in_array`` rule.
