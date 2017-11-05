======================
Rule ``no_unset_cast``
======================

Variables must be set ``null`` instead of using ``(unset)`` casting.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$a = (unset) $b;
   +$a =  null;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_unset_cast`` rule.

@PHP80Migration
  Using the ``@PHP80Migration`` rule set will enable the ``no_unset_cast`` rule.
