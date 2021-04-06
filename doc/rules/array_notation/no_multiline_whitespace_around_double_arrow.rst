====================================================
Rule ``no_multiline_whitespace_around_double_arrow``
====================================================

Operator ``=>`` should not be surrounded by multi-line whitespaces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = array(1
   -
   -=> 2);
   +$a = array(1 => 2);

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_multiline_whitespace_around_double_arrow`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_multiline_whitespace_around_double_arrow`` rule.
