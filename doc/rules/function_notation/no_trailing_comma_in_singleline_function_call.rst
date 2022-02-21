======================================================
Rule ``no_trailing_comma_in_singleline_function_call``
======================================================

When making a method or function call on a single line there MUST NOT be a
trailing comma after the last argument.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -foo($a,);
   +foo($a);

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_trailing_comma_in_singleline_function_call`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_trailing_comma_in_singleline_function_call`` rule.
