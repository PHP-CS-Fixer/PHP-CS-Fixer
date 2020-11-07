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
   @@ -1,8 +1,8 @@
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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_indent`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_indent`` rule.
