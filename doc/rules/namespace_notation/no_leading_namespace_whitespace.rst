========================================
Rule ``no_leading_namespace_whitespace``
========================================

The namespace declaration line shouldn't contain leading whitespace.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
   - namespace Test8a;
   -    namespace Test8b;
   +namespace Test8a;
   +namespace Test8b;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_leading_namespace_whitespace`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_leading_namespace_whitespace`` rule.
