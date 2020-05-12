====================================
Rule ``no_blank_lines_after_phpdoc``
====================================

There should not be blank lines between docblock and the documented element.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /**
     * This is the bar class.
     */
   -
   -
    class Bar {}

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_blank_lines_after_phpdoc`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_blank_lines_after_phpdoc`` rule.
