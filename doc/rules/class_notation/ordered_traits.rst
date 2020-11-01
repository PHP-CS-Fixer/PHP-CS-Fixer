=======================
Rule ``ordered_traits``
=======================

Trait ``use`` statements must be sorted alphabetically.

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

@Symfony
  Using the ``@Symfony`` rule set will enable the ``ordered_traits`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``ordered_traits`` rule.
