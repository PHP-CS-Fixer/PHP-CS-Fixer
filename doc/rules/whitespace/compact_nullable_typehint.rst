==================================
Rule ``compact_nullable_typehint``
==================================

Remove extra spaces in a nullable typehint.

Description
-----------

Rule is applied only in a PHP 7.1+ environment.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(? string $str): ? string
   +function sample(?string $str): ?string
    {}

Rule sets
---------

The rule is part of the following rule sets:

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``compact_nullable_typehint`` rule.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``compact_nullable_typehint`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``compact_nullable_typehint`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``compact_nullable_typehint`` rule.
