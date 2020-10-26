=======================================
Rule ``no_trailing_comma_in_list_call``
=======================================

Remove trailing commas in list function calls.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -list($a, $b,) = foo();
   +list($a, $b) = foo();

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_trailing_comma_in_list_call`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_trailing_comma_in_list_call`` rule.
