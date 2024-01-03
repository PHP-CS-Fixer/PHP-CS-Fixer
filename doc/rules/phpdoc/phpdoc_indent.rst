======================
Rule ``phpdoc_indent``
======================

Docblocks should have the same indentation as the documented subject.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class DocBlocks
    {
   -/**
   - * Test constants
   - */
   +    /**
   +     * Test constants
   +     */
        const INDENT = 1;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocIndentFixer <./../../../src/Fixer/Phpdoc/PhpdocIndentFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocIndentFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocIndentFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
