============================================
Rule ``phpdoc_var_annotation_correct_order``
============================================

``@var`` and ``@type`` annotations must have type and name in the correct order.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -/** @var $foo int */
   +/** @var int $foo */
    $foo = 2 + 2;

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_var_annotation_correct_order`` rule.
