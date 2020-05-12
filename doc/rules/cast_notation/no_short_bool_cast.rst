===========================
Rule ``no_short_bool_cast``
===========================

Short cast ``bool`` using double exclamation mark should not be used.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = !!$b;
   +$a = (bool)$b;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_short_bool_cast`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_short_bool_cast`` rule.
