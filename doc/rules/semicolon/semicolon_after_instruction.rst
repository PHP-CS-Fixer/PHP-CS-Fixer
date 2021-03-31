====================================
Rule ``semicolon_after_instruction``
====================================

Instructions must be terminated with a semicolon.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php echo 1 ?>
   +<?php echo 1; ?>

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``semicolon_after_instruction`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``semicolon_after_instruction`` rule.
