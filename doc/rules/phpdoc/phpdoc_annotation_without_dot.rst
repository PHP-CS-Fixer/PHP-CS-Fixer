======================================
Rule ``phpdoc_annotation_without_dot``
======================================

PHPDoc annotation descriptions should not be a sentence.

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
   - * @param string $bar Some string.
   + * @param string $bar some string
     */
    function foo ($bar) {}

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_annotation_without_dot`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_annotation_without_dot`` rule.
