==============================================
Rule ``no_trailing_comma_in_singleline_array``
==============================================

PHP single-line arrays should not have trailing comma.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = array('sample',  );
   +$a = array('sample');

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_trailing_comma_in_singleline_array`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_trailing_comma_in_singleline_array`` rule.
