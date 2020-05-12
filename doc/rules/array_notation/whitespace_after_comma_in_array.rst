========================================
Rule ``whitespace_after_comma_in_array``
========================================

In array declaration, there MUST be a whitespace after each comma.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = array(1,'a',$b,);
   +$sample = array(1, 'a', $b, );

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``whitespace_after_comma_in_array`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``whitespace_after_comma_in_array`` rule.
