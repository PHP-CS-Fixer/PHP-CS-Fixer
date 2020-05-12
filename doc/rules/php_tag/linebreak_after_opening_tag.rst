====================================
Rule ``linebreak_after_opening_tag``
====================================

Ensure there is no code on the same line as the PHP open tag.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = 1;
   +<?php
   +$a = 1;
    $b = 3;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``linebreak_after_opening_tag`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``linebreak_after_opening_tag`` rule.
