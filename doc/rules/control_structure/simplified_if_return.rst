=============================
Rule ``simplified_if_return``
=============================

Simplify ``if`` control structures that return the boolean result of their
condition.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -if ($foo) { return true; } return false;
   +return (bool) ($foo)      ;
