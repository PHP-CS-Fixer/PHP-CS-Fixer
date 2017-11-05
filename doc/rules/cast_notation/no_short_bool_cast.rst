===========================
Rule ``no_short_bool_cast``
===========================

Short cast ``bool`` using double exclamation mark should not be used.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$a = !!$b;
   +$a = (bool)$b;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_short_bool_cast`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_short_bool_cast`` rule.
