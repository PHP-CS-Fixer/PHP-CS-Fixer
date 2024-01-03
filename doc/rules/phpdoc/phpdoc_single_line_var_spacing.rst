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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocSingleLineVarSpacingFixer <./../../../src/Fixer/Phpdoc/PhpdocSingleLineVarSpacingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocSingleLineVarSpacingFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocSingleLineVarSpacingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
