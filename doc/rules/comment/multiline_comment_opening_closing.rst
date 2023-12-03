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

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Comment\\MultilineCommentOpeningClosingFixer <./../../../src/Fixer/Comment/MultilineCommentOpeningClosingFixer.php>`_
