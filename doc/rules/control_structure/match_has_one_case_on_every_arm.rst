========================================
Rule ``match_has_one_case_on_every_arm``
========================================

Match only allow has one case on every arm.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
                    return   match ($bar) {
                        2 => "c",
   -                    3,4,5 =>"e",
   +                    3,
   +                    4,
   +                    5 =>"e",
                        default => "d"
                    };
