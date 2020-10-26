==============================================
Rule ``no_trailing_comma_in_singleline_array``
==============================================

PHP single-line arrays should not have trailing comma.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$a = array('sample',  );
   +$a = array('sample');

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_trailing_comma_in_singleline_array`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_trailing_comma_in_singleline_array`` rule.
