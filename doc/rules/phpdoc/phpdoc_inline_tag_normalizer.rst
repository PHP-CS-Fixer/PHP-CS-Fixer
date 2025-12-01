=====================================
Rule ``phpdoc_inline_tag_normalizer``
=====================================

Fixes PHPDoc inline tags.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``tags``.

Configuration
-------------

``tags``
~~~~~~~~

The list of tags to normalize.

Allowed types: ``list<string>``

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocInlineTagNormalizerFixer <./../../../src/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocInlineTagNormalizerFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
