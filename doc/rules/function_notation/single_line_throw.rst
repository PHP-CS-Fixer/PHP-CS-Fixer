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
    <?php
   -throw new Exception(
   -    'Error.',
   -    500
   -);
   +throw new Exception('Error.', 500);

Rule sets
---------

The rule is part of the following rule set:

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``single_line_throw`` rule.
