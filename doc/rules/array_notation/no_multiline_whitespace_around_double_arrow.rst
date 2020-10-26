====================================================
Rule ``no_multiline_whitespace_around_double_arrow``
====================================================

Operator ``=>`` should not be surrounded by multi-line whitespaces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,2 @@
    <?php
   -$a = array(1
   -
   -=> 2);
   +$a = array(1 => 2);

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_multiline_whitespace_around_double_arrow`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_multiline_whitespace_around_double_arrow`` rule.
