=================================================
Rule ``assign_null_coalescing_to_coalesce_equal``
=================================================

Use the null coalescing assignment operator ``??=`` where possible.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = $foo ?? 1;
   +$foo ??= 1;

Rule sets
---------

The rule is part of the following rule sets:

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``assign_null_coalescing_to_coalesce_equal`` rule.

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``assign_null_coalescing_to_coalesce_equal`` rule.

@PHP81Migration
  Using the `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ rule set will enable the ``assign_null_coalescing_to_coalesce_equal`` rule.

@PHP82Migration
  Using the `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ rule set will enable the ``assign_null_coalescing_to_coalesce_equal`` rule.
