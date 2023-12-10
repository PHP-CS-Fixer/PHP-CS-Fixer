====================================
Rule ``single_line_comment_spacing``
====================================

Single-line comments must have proper spacing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -//comment 1
   -#comment 2
   -/*comment 3*/
   +// comment 1
   +# comment 2
   +/* comment 3 */

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Comment\\SingleLineCommentSpacingFixer <./../../../src/Fixer/Comment/SingleLineCommentSpacingFixer.php>`_
