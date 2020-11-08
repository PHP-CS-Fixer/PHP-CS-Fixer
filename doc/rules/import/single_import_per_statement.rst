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
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``single_import_per_statement`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``single_import_per_statement`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``single_import_per_statement`` rule.
