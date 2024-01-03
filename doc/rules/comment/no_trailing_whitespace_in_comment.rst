==========================================
Rule ``no_trailing_whitespace_in_comment``
==========================================

There MUST be no trailing spaces inside comment or PHPDoc.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -// This is 
   -// a comment. 
   +// This is
   +// a comment.

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Comment\\NoTrailingWhitespaceInCommentFixer <./../../../src/Fixer/Comment/NoTrailingWhitespaceInCommentFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Comment\\NoTrailingWhitespaceInCommentFixerTest <./../../../tests/Fixer/Comment/NoTrailingWhitespaceInCommentFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
