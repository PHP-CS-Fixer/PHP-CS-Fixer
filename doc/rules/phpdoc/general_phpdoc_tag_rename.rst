==================================
Rule ``general_phpdoc_tag_rename``
==================================

Renames PHPDoc tags.

Configuration
-------------

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Whether tags should be replaced only if they have exact same casing.

Allowed types: ``bool``

Default value: ``false``

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

Allowed types: ``array<string, string>``

Default value: ``[]``

Examples
--------

Example #1
~~~~~~~~~~

With configuration: ``['replacements' => ['inheritDocs' => 'inheritDoc']]``.

.. code-block:: diff

   --- Original
   +++ New
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
    <?php
    /**
   - * @inheritDocs
   + * @inheritDoc
     * {@inheritdocs}
     */

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['replacements' => ['inheritDocs' => 'inheritDoc']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['replacements' => ['inheritDocs' => 'inheritDoc']]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\GeneralPhpdocTagRenameFixer <./../../../src/Fixer/Phpdoc/GeneralPhpdocTagRenameFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\GeneralPhpdocTagRenameFixerTest <./../../../tests/Fixer/Phpdoc/GeneralPhpdocTagRenameFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
