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

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_indent`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_indent`` rule.
