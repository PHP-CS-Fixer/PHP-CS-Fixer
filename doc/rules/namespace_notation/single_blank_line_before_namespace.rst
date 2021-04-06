===========================================
Rule ``single_blank_line_before_namespace``
===========================================

There should be exactly one blank line before a namespace declaration.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php  namespace A {}
   +<?php
   +
   +namespace A {}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -
    namespace A{}

Rule sets
---------

The rule is part of the following rule sets:

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``single_blank_line_before_namespace`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``single_blank_line_before_namespace`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``single_blank_line_before_namespace`` rule.
