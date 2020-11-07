==================================
Rule ``general_phpdoc_tag_rename``
==================================

Renames PHPDoc tags.

Configuration
-------------

``fix_annotation``
~~~~~~~~~~~~~~~~~~

Whether annotation tags should be fixed.

Allowed types: ``bool``

Default value: ``true``

``fix_inline``
~~~~~~~~~~~~~~

Whether inline tags should be fixed.

Allowed types: ``bool``

Default value: ``true``

``replacements``
~~~~~~~~~~~~~~~~

A map of tags to replace.

Allowed types: ``array``

Default value: ``[]``

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Whether tags should be replaced only if they have exact same casing.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

With configuration: ``['replacements' => ['inheritDocs' => 'inheritDoc']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    /**
   - * @inheritDocs
   - * {@inheritdocs}
   + * @inheritDoc
   + * {@inheritDoc}
     */

Example #2
~~~~~~~~~~

With configuration: ``['replacements' => ['inheritDocs' => 'inheritDoc'], 'fix_annotation' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    /**
     * @inheritDocs
   - * {@inheritdocs}
   + * {@inheritDoc}
     */

Example #3
~~~~~~~~~~

With configuration: ``['replacements' => ['inheritDocs' => 'inheritDoc'], 'fix_inline' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    /**
   - * @inheritDocs
   + * @inheritDoc
     * {@inheritdocs}
     */

Example #4
~~~~~~~~~~

With configuration: ``['replacements' => ['inheritDocs' => 'inheritDoc'], 'case_sensitive' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    /**
   - * @inheritDocs
   + * @inheritDoc
     * {@inheritdocs}
     */

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``general_phpdoc_tag_rename`` rule with the config below:

  ``['replacements' => ['inheritDocs' => 'inheritDoc']]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``general_phpdoc_tag_rename`` rule with the config below:

  ``['replacements' => ['inheritDocs' => 'inheritDoc']]``
