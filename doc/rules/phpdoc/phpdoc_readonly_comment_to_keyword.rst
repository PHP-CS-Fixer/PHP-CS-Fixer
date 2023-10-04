===========================================
Rule ``phpdoc_readonly_comment_to_keyword``
===========================================

Converts readonly comment to readonly keyword.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
        <?php
   -    /** @readonly */
   -    class C {
   +    
   +    readonly class C {
        }
