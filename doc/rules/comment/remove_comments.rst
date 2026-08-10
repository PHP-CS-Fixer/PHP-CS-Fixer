========================
Rule ``remove_comments``
========================

Removes comments that are preceded by ``;`` (semicolon).

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php echo 123; /* Comment */
   +<?php echo 123; 

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Comment\\RemoveCommentsFixer <./../../../src/Fixer/Comment/RemoveCommentsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Comment\\RemoveCommentsFixerTest <./../../../tests/Fixer/Comment/RemoveCommentsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
