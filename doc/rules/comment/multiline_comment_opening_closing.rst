==========================================
Rule ``multiline_comment_opening_closing``
==========================================

DocBlocks must start with two asterisks, multiline comments must start with a
single asterisk, after the opening slash. Both must end with a single asterisk
before the closing slash.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,13 +1,13 @@
    <?php

   -/******
   +/*
     * Multiline comment with arbitrary asterisks count
   - ******/
   + */

   -/**\
   +/*\
     * Multiline comment that seems a DocBlock
     */

    /**
     * DocBlock with arbitrary asterisk count at the end
   - **/
   + */

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``multiline_comment_opening_closing`` rule.
