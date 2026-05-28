==========================
Rule ``phpdoc_tag_casing``
==========================

Fixes casing of PHPDoc tags.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``tags``.

Configuration
-------------

``tags``
~~~~~~~~

List of tags to fix with their expected casing.

Allowed types: ``list<string>``

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTagCasingFixer <./../../../src/Fixer/Phpdoc/PhpdocTagCasingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocTagCasingFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocTagCasingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
