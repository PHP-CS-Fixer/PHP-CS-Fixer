=======================================
Rule ``no_trailing_comma_in_list_call``
=======================================

Remove trailing commas in list function calls.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -list($a, $b,) = foo();
   +list($a, $b) = foo();

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_trailing_comma_in_list_call`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_trailing_comma_in_list_call`` rule.
