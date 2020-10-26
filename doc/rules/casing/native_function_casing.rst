===============================
Rule ``native_function_casing``
===============================

Function defined by PHP should be called using the correct casing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -STRLEN($str);
   +strlen($str);

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``native_function_casing`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``native_function_casing`` rule.
