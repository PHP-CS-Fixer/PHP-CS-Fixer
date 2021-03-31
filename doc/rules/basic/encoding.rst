=================
Rule ``encoding``
=================

PHP code MUST use only UTF-8 without BOM (remove BOM).

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -﻿<?php
   +<?php

    echo "Hello!";

Rule sets
---------

The rule is part of the following rule sets:

@PSR1
  Using the `@PSR1 <./../../ruleSets/PSR1.rst>`_ rule set will enable the ``encoding`` rule.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``encoding`` rule.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``encoding`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``encoding`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``encoding`` rule.
