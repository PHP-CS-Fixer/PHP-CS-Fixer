===================================================
Rule ``no_singleline_whitespace_before_semicolons``
===================================================

Single-line whitespace before closing semicolon are prohibited.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $this->foo() ;
   +<?php $this->foo();

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_singleline_whitespace_before_semicolons`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_singleline_whitespace_before_semicolons`` rule.
