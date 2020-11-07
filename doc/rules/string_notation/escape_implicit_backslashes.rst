====================================
Rule ``escape_implicit_backslashes``
====================================

Escape implicit backslashes in strings and heredocs to ease the understanding of
which are special chars interpreted by PHP and which not.

Description
-----------

In PHP double-quoted strings and heredocs some chars like ``n``, ``$`` or ``u``
have special meanings if preceded by a backslash (and some are special only if
followed by other special chars), while a backslash preceding other chars are
interpreted like a plain backslash. The precise list of those special chars is
hard to remember and to identify quickly: this fixer escapes backslashes that do
not start a special interpretation with the char after them.
It is possible to fix also single-quoted strings: in this case there is no
special chars apart from single-quote and backslash itself, so the fixer simply
ensure that all backslashes are escaped. Both single and double backslashes are
allowed in single-quoted strings, so the purpose in this context is mainly to
have a uniformed way to have them written all over the codebase.

Configuration
-------------

``single_quoted``
~~~~~~~~~~~~~~~~~

Whether to fix single-quoted strings.

Allowed types: ``bool``

Default value: ``false``

``double_quoted``
~~~~~~~~~~~~~~~~~

Whether to fix double-quoted strings.

Allowed types: ``bool``

Default value: ``true``

``heredoc_syntax``
~~~~~~~~~~~~~~~~~~

Whether to fix heredoc syntax.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,8 +2,8 @@

    $singleQuoted = 'String with \" and My\Prefix\\';

   -$doubleQuoted = "Interpret my \n but not my \a";
   +$doubleQuoted = "Interpret my \n but not my \\a";

    $hereDoc = <<<HEREDOC
   -Interpret my \100 but not my \999
   +Interpret my \100 but not my \\999
    HEREDOC;

Example #2
~~~~~~~~~~

With configuration: ``['single_quoted' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,9 +1,9 @@
    <?php

   -$singleQuoted = 'String with \" and My\Prefix\\';
   +$singleQuoted = 'String with \\" and My\\Prefix\\';

   -$doubleQuoted = "Interpret my \n but not my \a";
   +$doubleQuoted = "Interpret my \n but not my \\a";

    $hereDoc = <<<HEREDOC
   -Interpret my \100 but not my \999
   +Interpret my \100 but not my \\999
    HEREDOC;

Example #3
~~~~~~~~~~

With configuration: ``['double_quoted' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -5,5 +5,5 @@
    $doubleQuoted = "Interpret my \n but not my \a";

    $hereDoc = <<<HEREDOC
   -Interpret my \100 but not my \999
   +Interpret my \100 but not my \\999
    HEREDOC;

Example #4
~~~~~~~~~~

With configuration: ``['heredoc_syntax' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,8 +2,8 @@

    $singleQuoted = 'String with \" and My\Prefix\\';

   -$doubleQuoted = "Interpret my \n but not my \a";
   +$doubleQuoted = "Interpret my \n but not my \\a";

    $hereDoc = <<<HEREDOC
    Interpret my \100 but not my \999
    HEREDOC;

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``escape_implicit_backslashes`` rule with the default config.
