=======================
Rule ``phpdoc_summary``
=======================

PHPDoc summary should end in either a full stop, exclamation mark, or question
mark.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * Foo function is great
   + * Foo function is great.
     */
    function foo () {}

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocSummaryFixer <./../../../src/Fixer/Phpdoc/PhpdocSummaryFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocSummaryFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocSummaryFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
