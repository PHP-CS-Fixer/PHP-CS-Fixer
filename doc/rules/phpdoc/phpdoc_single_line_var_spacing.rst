=======================================
Rule ``phpdoc_single_line_var_spacing``
=======================================

Single line ``@var`` PHPDoc should have proper spacing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php /**@var   MyClass   $a   */
   +<?php /** @var MyClass $a */
    $a = test();

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_single_line_var_spacing`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_single_line_var_spacing`` rule.
