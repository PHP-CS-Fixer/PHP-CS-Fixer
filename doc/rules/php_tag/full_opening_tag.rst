=========================
Rule ``full_opening_tag``
=========================

PHP code must use the long ``<?php`` tags or short-echo ``<?=`` tags and not
other tag variations.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?
   +<?php

    echo "Hello!";

Rule sets
---------

The rule is part of the following rule sets:

@PSR1
  Using the `@PSR1 <./../../ruleSets/PSR1.rst>`_ rule set will enable the ``full_opening_tag`` rule.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``full_opening_tag`` rule.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``full_opening_tag`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``full_opening_tag`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``full_opening_tag`` rule.
