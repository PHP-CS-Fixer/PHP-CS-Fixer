=====================================
Rule ``blank_line_after_opening_tag``
=====================================

Ensure there is no code on the same line as the PHP open tag and it is followed
by a blank line.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = 1;
   +<?php
   +
   +$a = 1;
    $b = 1;

Rule sets
---------

The rule is part of the following rule sets:

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``blank_line_after_opening_tag`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``blank_line_after_opening_tag`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``blank_line_after_opening_tag`` rule.
