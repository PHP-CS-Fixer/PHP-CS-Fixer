========================
Rule ``echo_tag_syntax``
========================

Replaces short-echo ``<?=`` with long format ``<?php echo``/``<?php print``
syntax, or vice-versa.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``format``,
``ignore_start_of_file``, ``ignore_tag_without_closing_tag``, ``long_function``,
``shorten_simple_statements_only``, ``shorten_tags_without_comments_only``.

Configuration
-------------

``format``
~~~~~~~~~~

The desired language construct.

Allowed values: ``'long'`` and ``'short'``

Default value: ``'long'``

``ignore_start_of_file``
~~~~~~~~~~~~~~~~~~~~~~~~

Ignore the tag at the first line of the file.

Allowed types: ``bool``

Default value: ``false``

``ignore_tag_without_closing_tag``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Ignore tags that do not have a closing ?> tag.

Allowed types: ``bool``

Default value: ``false``

``long_function``
~~~~~~~~~~~~~~~~~

The function to be used to expand the short echo tags.

Allowed values: ``'echo'`` and ``'print'``

Default value: ``'echo'``

``shorten_simple_statements_only``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Render short-echo tags only in case of simple code.

Allowed types: ``bool``

Default value: ``true``

``shorten_tags_without_comments_only``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Render short-echo tags only if the tag doesn't contain a comment.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php print '2' . '3'; ?>
   -<?=1?>
   +<?php echo 1?>
    <?php /* comment */ echo '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>
    <?php print '2' . '3';

Example #2
~~~~~~~~~~

With configuration: ``['format' => 'long']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php print '2' . '3'; ?>
   -<?=1?>
   +<?php echo 1?>
    <?php /* comment */ echo '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>
    <?php print '2' . '3';

Example #3
~~~~~~~~~~

With configuration: ``['format' => 'long', 'long_function' => 'print']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php print '2' . '3'; ?>
   -<?=1?>
   +<?php print 1?>
    <?php /* comment */ echo '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>
    <?php print '2' . '3';

Example #4
~~~~~~~~~~

With configuration: ``['format' => 'short']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php print '2' . '3'; ?>
   +<?= '2' . '3'; ?>
    <?=1?>
   -<?php /* comment */ echo '2' . '3'; ?>
   +<?=/* comment */ '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>
   -<?php print '2' . '3';
   +<?= '2' . '3';

Example #5
~~~~~~~~~~

With configuration: ``['format' => 'short', 'shorten_simple_statements_only' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php print '2' . '3'; ?>
   +<?= '2' . '3'; ?>
    <?=1?>
   -<?php /* comment */ echo '2' . '3'; ?>
   -<?php print '2' . '3'; someFunction(); ?>
   -<?php print '2' . '3';
   +<?=/* comment */ '2' . '3'; ?>
   +<?= '2' . '3'; someFunction(); ?>
   +<?= '2' . '3';

Example #6
~~~~~~~~~~

With configuration: ``['format' => 'short', 'ignore_start_of_file' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php print '2' . '3'; ?>
    <?=1?>
   -<?php /* comment */ echo '2' . '3'; ?>
   +<?=/* comment */ '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>
   -<?php print '2' . '3';
   +<?= '2' . '3';

Example #7
~~~~~~~~~~

With configuration: ``['format' => 'short', 'ignore_tag_without_closing_tag' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php print '2' . '3'; ?>
   +<?= '2' . '3'; ?>
    <?=1?>
   -<?php /* comment */ echo '2' . '3'; ?>
   +<?=/* comment */ '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>
    <?php print '2' . '3';

Example #8
~~~~~~~~~~

With configuration: ``['format' => 'short', 'shorten_tags_without_comments_only' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php print '2' . '3'; ?>
   +<?= '2' . '3'; ?>
    <?=1?>
    <?php /* comment */ echo '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>
   -<?php print '2' . '3';
   +<?= '2' . '3';

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpTag\\EchoTagSyntaxFixer <./../../../src/Fixer/PhpTag/EchoTagSyntaxFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpTag\\EchoTagSyntaxFixerTest <./../../../tests/Fixer/PhpTag/EchoTagSyntaxFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
