=========================
Rule ``no_empty_comment``
=========================

There should not be any empty comments.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -//
   -#
   -/* */
   +
   +
   +

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Comment\\NoEmptyCommentFixer <./../../../src/Fixer/Comment/NoEmptyCommentFixer.php>`_
