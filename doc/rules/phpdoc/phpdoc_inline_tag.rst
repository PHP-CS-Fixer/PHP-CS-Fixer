==========================
Rule ``phpdoc_inline_tag``
==========================

Fix PHPDoc inline tags, make ``@inheritdoc`` always inline.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,7 @@
    <?php
    /**
   - * @{TUTORIAL}
   - * {{ @link }}
   - * {@examples}
   - * @inheritdocs
   + * {@tutorial}
   + * {@link}
   + * {@example}
   + * {@inheritdoc}
     */

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_inline_tag`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_inline_tag`` rule.
