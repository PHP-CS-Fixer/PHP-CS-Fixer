=============================
Rule ``integer_literal_case``
=============================

Integer literals must be in correct case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = 0Xff;
   -$bar = 0B11111111;
   +$foo = 0xFF;
   +$bar = 0b11111111;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``integer_literal_case`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``integer_literal_case`` rule.
