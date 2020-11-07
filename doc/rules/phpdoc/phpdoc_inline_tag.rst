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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_inline_tag`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_inline_tag`` rule.
