=======================
Rule ``phpdoc_summary``
=======================

PHPDoc summary should end in either a full stop, exclamation mark, or question
mark.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    /**
   - * Foo function is great
   + * Foo function is great.
     */
    function foo () {}

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_summary`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_summary`` rule.
