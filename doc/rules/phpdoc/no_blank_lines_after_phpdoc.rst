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
   @@ -3,6 +3,4 @@
    /**
     * This is the bar class.
     */
   -
   -
    class Bar {}

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_blank_lines_after_phpdoc`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_blank_lines_after_phpdoc`` rule.
