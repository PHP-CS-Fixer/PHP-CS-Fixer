=================================
Rule ``switch_continue_to_break``
=================================

Switch case must not be ended with ``continue`` but with ``break``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    switch ($foo) {
        case 1:
   -        continue;
   +        break;
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,7 +3,7 @@
        case 1:
            while($bar) {
                do {
   -                continue 3;
   +                break 3;
                } while(false);

                if ($foo + 1 > 3) {
   @@ -10,6 +10,6 @@
                    continue;
                }

   -            continue 2;
   +            break 2;
            }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``switch_continue_to_break`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``switch_continue_to_break`` rule.
