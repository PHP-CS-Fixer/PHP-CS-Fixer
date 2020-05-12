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

Allowed values: ``'all_multiline'``, ``'phpdocs_like'``, ``'phpdocs_only'``

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

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``align_multiline_comment`` rule with the default config.
