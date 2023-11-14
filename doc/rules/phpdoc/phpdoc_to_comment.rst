==========================
Rule ``phpdoc_to_comment``
==========================

Docblocks should only be used on structural elements.

Configuration
-------------

``ignored_tags``
~~~~~~~~~~~~~~~~

List of ignored tags (matched case insensitively).

Allowed types: ``array``

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

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocToCommentFixer <./../src/Fixer/Phpdoc/PhpdocToCommentFixer.php>`_
