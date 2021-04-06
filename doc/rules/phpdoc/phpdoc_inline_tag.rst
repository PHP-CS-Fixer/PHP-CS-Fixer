==========================
Rule ``phpdoc_inline_tag``
==========================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``general_phpdoc_tag_rename``,
   ``phpdoc_inline_tag_normalizer`` and ``phpdoc_tag_type`` instead.

Fix PHPDoc inline tags, make ``@inheritdoc`` always inline.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @{TUTORIAL}
   - * {{ @link }}
   - * {@examples}
   - * @inheritdocs
   + * {@TUTORIAL}
   + * {@link}
   + * {@example}
   + * {@inheritdoc}
     */
