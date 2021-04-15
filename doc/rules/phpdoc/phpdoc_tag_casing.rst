==========================
Rule ``phpdoc_tag_casing``
==========================

`src <../../../src/Fixer/Phpdoc/PhpdocTagCasingFixer.php>`_

Fixes casing of PHPDoc tags.

Configuration
-------------

``tags``
~~~~~~~~

List of tags to fix with their expected casing.

Allowed types: ``array``

Default value: ``['inheritDoc']``

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
   - * @inheritdoc
   + * @inheritDoc
     */

Example #2
~~~~~~~~~~

With configuration: ``['tags' => ['foo']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @inheritdoc
   - * @Foo
   + * @foo
     */
