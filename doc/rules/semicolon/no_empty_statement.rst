===========================
Rule ``no_empty_statement``
===========================

Remove useless (semicolon) statements.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $a = 1;;
   +<?php $a = 1;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php echo 1;2;
   +<?php echo 1;

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php while(foo()){
   -    continue 1;
   +    continue ;
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_empty_statement`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_empty_statement`` rule.
