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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Comment\\NoEmptyCommentFixer <./../../../src/Fixer/Comment/NoEmptyCommentFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Comment\\NoEmptyCommentFixerTest <./../../../tests/Fixer/Comment/NoEmptyCommentFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
