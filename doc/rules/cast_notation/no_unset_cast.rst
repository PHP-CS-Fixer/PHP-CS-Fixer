======================
Rule ``no_unset_cast``
======================

Variables must be set ``null`` instead of using ``(unset)`` casting.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = (unset) $b;
   +$a =  null;

Rule sets
---------

The rule is part of the following rule sets:

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``no_unset_cast`` rule.

@PHP81Migration
  Using the `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ rule set will enable the ``no_unset_cast`` rule.

@PHP82Migration
  Using the `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ rule set will enable the ``no_unset_cast`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_unset_cast`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_unset_cast`` rule.
