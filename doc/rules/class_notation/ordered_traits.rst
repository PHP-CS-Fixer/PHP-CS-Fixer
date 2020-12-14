=======================
Rule ``ordered_traits``
=======================

Trait ``use`` statements must be sorted alphabetically.

.. warning:: Using this rule is risky.

   Risky when depending on order of the imports.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php class Foo { 
   -use Z; use A; }
   +use A; use Z; }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``ordered_traits`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``ordered_traits`` rule.
