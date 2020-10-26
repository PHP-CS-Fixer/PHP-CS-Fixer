==============================
Rule ``magic_constant_casing``
==============================

Magic constants should be referred to using the correct casing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -echo __dir__;
   +echo __DIR__;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``magic_constant_casing`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``magic_constant_casing`` rule.
