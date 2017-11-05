==========================
Rule ``no_useless_return``
==========================

There should not be an empty ``return`` statement at the end of a function.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,5 +3,5 @@
        if ($b) {
            return;
        }
   -    return;
   +    
    }

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_useless_return`` rule.
