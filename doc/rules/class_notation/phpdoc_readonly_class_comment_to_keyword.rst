=================================================
Rule ``phpdoc_readonly_class_comment_to_keyword``
=================================================

Converts readonly comment on classes to the readonly keyword.

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
