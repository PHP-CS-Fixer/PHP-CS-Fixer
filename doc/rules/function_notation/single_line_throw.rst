==========================
Rule ``single_line_throw``
==========================

Throwing exception must be done in single line.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,2 @@
    <?php
   -throw new Exception(
   -    'Error.',
   -    500
   -);
   +throw new Exception('Error.', 500);
