==============================
Rule ``normalize_index_brace``
==============================

Array index should always be written by using square braces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -echo $sample{$index};
   +echo $sample[$index];

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``normalize_index_brace`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``normalize_index_brace`` rule.

@PHP80Migration
  Using the ``@PHP80Migration`` rule set will enable the ``normalize_index_brace`` rule.
