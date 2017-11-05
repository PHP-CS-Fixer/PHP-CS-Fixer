====================================
Rule ``linebreak_after_opening_tag``
====================================

Ensure there is no code on the same line as the PHP open tag.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,3 @@
   -<?php $a = 1;
   +<?php
   +$a = 1;
    $b = 3;
