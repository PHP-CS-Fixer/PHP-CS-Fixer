==========================
Rule ``phpdoc_to_comment``
==========================

Docblocks should only be used on structural elements.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options:
``allow_before_return_statement``, ``ignored_tags``.

Configuration
-------------

``allow_before_return_statement``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to allow PHPDoc before return statement.

Allowed types: ``bool``

Default value: ``false``

``ignored_tags``
~~~~~~~~~~~~~~~~

List of ignored tags (matched case insensitively).

Allowed types: ``list<string>``

Default value: ``[]``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $first = true;// needed because by default first docblock is never fixed.

   -/** This should be a comment */
   +/* This should be a comment */
    foreach($connections as $key => $sqlite) {
        $sqlite->open($path);
    }

Example #2
~~~~~~~~~~

With configuration: ``['ignored_tags' => ['todo']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $first = true;// needed because by default first docblock is never fixed.

   -/** This should be a comment */
   +/* This should be a comment */
    foreach($connections as $key => $sqlite) {
        $sqlite->open($path);
    }

    /** @todo This should be a PHPDoc as the tag is on "ignored_tags" list */
    foreach($connections as $key => $sqlite) {
        $sqlite->open($path);
    }

Example #3
~~~~~~~~~~

With configuration: ``['allow_before_return_statement' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $first = true;// needed because by default first docblock is never fixed.

   -/** This should be a comment */
   +/* This should be a comment */
    foreach($connections as $key => $sqlite) {
        $sqlite->open($path);
    }

    function returnClassName() {
        /** @var class-string */
        return \StdClass::class;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['allow_before_return_statement' => false]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['allow_before_return_statement' => false]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocToCommentFixer <./../../../src/Fixer/Phpdoc/PhpdocToCommentFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocToCommentFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocToCommentFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
