================================
Rule ``align_multiline_comment``
================================

Each line of multi-line DocComments must have an asterisk [PSR-5] and must be
aligned with the first one.

Configuration
-------------

``comment_type``
~~~~~~~~~~~~~~~~

Whether to fix PHPDoc comments only (``phpdocs_only``), any multi-line comment
whose lines all start with an asterisk (``phpdocs_like``) or any multi-line
comment (``all_multiline``).

Allowed values: ``'all_multiline'``, ``'phpdocs_like'`` and ``'phpdocs_only'``

Default value: ``'phpdocs_only'``

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
   -            * This is a DOC Comment
   -with a line not prefixed with asterisk
   -
   -   */
   +     * This is a DOC Comment
   +     * with a line not prefixed with asterisk
   +     *
   +     */

Example #2
~~~~~~~~~~

With configuration: ``['comment_type' => 'phpdocs_like']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        /*
   -            * This is a doc-like multiline comment
   -*/
   +     * This is a doc-like multiline comment
   +     */

Example #3
~~~~~~~~~~

With configuration: ``['comment_type' => 'all_multiline']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        /*
   -            * This is a doc-like multiline comment
   +     * This is a doc-like multiline comment
    with a line not prefixed with asterisk

   -   */
   +     */

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\AlignMultilineCommentFixer <./../../../src/Fixer/Phpdoc/AlignMultilineCommentFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\AlignMultilineCommentFixerTest <./../../../tests/Fixer/Phpdoc/AlignMultilineCommentFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
