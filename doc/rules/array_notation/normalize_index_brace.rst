==============================
Rule ``normalize_index_brace``
==============================

Array index should always be written by using square braces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -echo $sample{$index};
   +echo $sample[$index];

Rule sets
---------

The rule is part of the following rule sets:

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``normalize_index_brace`` rule.

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``normalize_index_brace`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``normalize_index_brace`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``normalize_index_brace`` rule.
