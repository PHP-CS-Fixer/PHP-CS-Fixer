===========================================
Rule ``object_operator_without_whitespace``
===========================================

There should not be space before or after object operators ``->`` and ``?->``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a  ->  b;
   +<?php $a->b;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``object_operator_without_whitespace`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``object_operator_without_whitespace`` rule.
