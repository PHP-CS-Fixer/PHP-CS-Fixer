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

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocVarAnnotationCorrectOrderFixer <./../../../src/Fixer/Phpdoc/PhpdocVarAnnotationCorrectOrderFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocVarAnnotationCorrectOrderFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocVarAnnotationCorrectOrderFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
