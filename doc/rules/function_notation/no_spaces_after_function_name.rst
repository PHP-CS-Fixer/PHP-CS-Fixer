======================================
Rule ``no_spaces_after_function_name``
======================================

When making a method or function call, there MUST NOT be a space between the
method or function name and the opening parenthesis.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
   -require ('sample.php');
   -echo (test (3));
   -exit  (1);
   -$func ();
   +require('sample.php');
   +echo(test(3));
   +exit(1);
   +$func();

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``no_spaces_after_function_name`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_spaces_after_function_name`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_spaces_after_function_name`` rule.
