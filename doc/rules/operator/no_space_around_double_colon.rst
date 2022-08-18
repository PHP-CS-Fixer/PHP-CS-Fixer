=====================================
Rule ``no_space_around_double_colon``
=====================================

There must be no space around double colons (also called Scope Resolution
Operator or Paamayim Nekudotayim).

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New

   -<?php echo Foo\Bar :: class;
   +<?php echo Foo\Bar::class;

Rule sets
---------

The rule is part of the following rule sets:

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``no_space_around_double_colon`` rule.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``no_space_around_double_colon`` rule.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``no_space_around_double_colon`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_space_around_double_colon`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_space_around_double_colon`` rule.
