======================
Rule ``phpdoc_compact_single_line``
======================

Docblocks that only have a single line of content should be in compact notation

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class DocBlocks
    {
   -    /**
   -     * foo
   -     */
   +    /** foo */
        const COMPACT = 1;
    }

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocCompactSingleLineFixer <./../../../src/Fixer/Phpdoc/PhpdocCompactSingleLineFixer.php>`_
