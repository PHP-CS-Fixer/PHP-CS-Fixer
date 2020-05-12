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

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``no_trailing_whitespace_in_comment`` rule.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``no_trailing_whitespace_in_comment`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_trailing_whitespace_in_comment`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_trailing_whitespace_in_comment`` rule.
