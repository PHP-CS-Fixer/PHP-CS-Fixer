===========================
Rule ``float_literal_case``
===========================

Float literals must be in correct case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = 1e80;
   -$bar = 2.5e-3;
   +$foo = 1E80;
   +$bar = 2.5E-3;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``float_literal_case`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``float_literal_case`` rule.
