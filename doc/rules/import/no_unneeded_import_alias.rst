=================================
Rule ``no_unneeded_import_alias``
=================================

Imports should not be aliased as the same name.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -use A\B\Foo as Foo;
   +use A\B\Foo  ;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_unneeded_import_alias`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_unneeded_import_alias`` rule.
