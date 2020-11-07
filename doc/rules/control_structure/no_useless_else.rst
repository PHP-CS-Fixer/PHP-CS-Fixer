========================
Rule ``no_useless_else``
========================

There should not be useless ``else`` cases.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,6 +1,6 @@
    <?php
    if ($a) {
        return 1;
   -} else {
   +}  
        return 2;
   -}
   +

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_useless_else`` rule.
