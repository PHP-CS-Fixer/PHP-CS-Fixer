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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_leading_namespace_whitespace`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_leading_namespace_whitespace`` rule.
