====================================
Rule ``single_import_per_statement``
====================================

There MUST be one use keyword per declaration.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,4 @@
    <?php
   -use Foo, Sample, Sample\Sample as Sample2;
   +use Foo;
   +use Sample;
   +use Sample\Sample as Sample2;

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``single_import_per_statement`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``single_import_per_statement`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``single_import_per_statement`` rule.
