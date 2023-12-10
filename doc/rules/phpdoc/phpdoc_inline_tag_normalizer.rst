=====================================
Rule ``phpdoc_inline_tag_normalizer``
=====================================

Fixes PHPDoc inline tags.

Configuration
-------------

``tags``
~~~~~~~~

The list of tags to normalize.

Allowed types: ``array``

Default value: ``['example', 'id', 'internal', 'inheritdoc', 'inheritdocs', 'link', 'source', 'toc', 'tutorial']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @{TUTORIAL}
   - * {{ @link }}
   + * {@TUTORIAL}
   + * {@link}
     * @inheritDoc
     */

Example #2
~~~~~~~~~~

With configuration: ``['tags' => ['TUTORIAL']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @{TUTORIAL}
   + * {@TUTORIAL}
     * {{ @link }}
     * @inheritDoc
     */

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocInlineTagNormalizerFixer <./../../../src/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixer.php>`_
