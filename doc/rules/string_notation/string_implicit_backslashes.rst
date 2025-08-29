====================================
Rule ``string_implicit_backslashes``
====================================

Handles implicit backslashes in strings and heredocs. Depending on the chosen
strategy, it can escape implicit backslashes to ease the understanding of which
are special chars interpreted by PHP and which not (``escape``), or it can
remove these additional backslashes if you find them superfluous (``unescape``).
You can also leave them as-is using ``ignore`` strategy.

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

``double_quoted``
~~~~~~~~~~~~~~~~~

Whether to escape backslashes in double-quoted strings.

Allowed values: ``'escape'``, ``'ignore'`` and ``'unescape'``

Default value: ``'escape'``

``heredoc``
~~~~~~~~~~~

Whether to escape backslashes in heredoc syntax.

Allowed values: ``'escape'``, ``'ignore'`` and ``'unescape'``

Default value: ``'escape'``

``single_quoted``
~~~~~~~~~~~~~~~~~

Whether to escape backslashes in single-quoted strings.

Allowed values: ``'escape'``, ``'ignore'`` and ``'unescape'``

Default value: ``'unescape'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    $singleQuoted = 'String with \" and My\Prefix\\';

   -$doubleQuoted = "Interpret my \n but not my \a";
   +$doubleQuoted = "Interpret my \n but not my \\a";

    $hereDoc = <<<HEREDOC
   -Interpret my \100 but not my \999
   +Interpret my \100 but not my \\999
    HEREDOC;

Example #2
~~~~~~~~~~

With configuration: ``['single_quoted' => 'escape']``.

.. code-block:: diff

   --- Original
   +++ New
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

With configuration: ``['double_quoted' => 'unescape']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    $singleQuoted = 'String with \" and My\Prefix\\';

    $doubleQuoted = "Interpret my \n but not my \a";

    $hereDoc = <<<HEREDOC
   -Interpret my \100 but not my \999
   +Interpret my \100 but not my \\999
    HEREDOC;

Example #4
~~~~~~~~~~

With configuration: ``['heredoc' => 'unescape']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    $singleQuoted = 'String with \" and My\Prefix\\';

   -$doubleQuoted = "Interpret my \n but not my \a";
   +$doubleQuoted = "Interpret my \n but not my \\a";

    $hereDoc = <<<HEREDOC
    Interpret my \100 but not my \999
    HEREDOC;

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\StringNotation\\StringImplicitBackslashesFixer <./../../../src/Fixer/StringNotation/StringImplicitBackslashesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\StringNotation\\StringImplicitBackslashesFixerTest <./../../../tests/Fixer/StringNotation/StringImplicitBackslashesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
