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

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocVarAnnotationCorrectOrderFixer <./../src/Fixer/Phpdoc/PhpdocVarAnnotationCorrectOrderFixer.php>`_
