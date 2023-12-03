=======================================
Rule ``phpdoc_single_line_var_spacing``
=======================================

Single line ``@var`` PHPDoc should have proper spacing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php /**@var   MyClass   $a   */
   +<?php /** @var MyClass $a */
    $a = test();

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocSingleLineVarSpacingFixer <./../../../src/Fixer/Phpdoc/PhpdocSingleLineVarSpacingFixer.php>`_
