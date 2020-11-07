===============================
Rule ``lambda_not_used_import``
===============================

Lambda must not import variables it doesn't use.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$foo = function() use ($bar) {};
   +$foo = function() {};

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``lambda_not_used_import`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``lambda_not_used_import`` rule.
