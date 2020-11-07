====================================
Rule ``no_whitespace_in_blank_line``
====================================

Remove trailing whitespace at the end of blank lines.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
   -   
   +
    $a = 1;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_whitespace_in_blank_line`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_whitespace_in_blank_line`` rule.
