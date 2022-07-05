========================================
Rule ``no_multiple_statements_per_line``
========================================

There must not be more than one statement per line.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -foo(); bar();
   +foo();
   +bar();
