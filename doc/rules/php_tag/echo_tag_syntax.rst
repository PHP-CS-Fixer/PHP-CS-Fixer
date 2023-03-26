========================
Rule ``echo_tag_syntax``
========================

Replaces short-echo ``<?=`` with long format ``<?php echo``/``<?php print``
syntax, or vice-versa.

Configuration
-------------

``format``
~~~~~~~~~~

The desired language construct.

Allowed values: ``'long'``, ``'short'``

Default value: ``'long'``

``long_function``
~~~~~~~~~~~~~~~~~

The function to be used to expand the short echo tags.

Allowed values: ``'echo'``, ``'print'``

Default value: ``'echo'``

``shorten_simple_statements_only``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Render short-echo tags only in case of simple code.

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
   -<?=1?>
   +<?php echo 1?>
    <?php print '2' . '3'; ?>
    <?php /* comment */ echo '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>

Example #2
~~~~~~~~~~

With configuration: ``['format' => 'long']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?=1?>
   +<?php echo 1?>
    <?php print '2' . '3'; ?>
    <?php /* comment */ echo '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>

Example #3
~~~~~~~~~~

With configuration: ``['format' => 'long', 'long_function' => 'print']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?=1?>
   +<?php print 1?>
    <?php print '2' . '3'; ?>
    <?php /* comment */ echo '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>

Example #4
~~~~~~~~~~

With configuration: ``['format' => 'short']``.

.. code-block:: diff

   --- Original
   +++ New
    <?=1?>
   -<?php print '2' . '3'; ?>
   -<?php /* comment */ echo '2' . '3'; ?>
   +<?= '2' . '3'; ?>
   +<?=/* comment */ '2' . '3'; ?>
    <?php print '2' . '3'; someFunction(); ?>

Example #5
~~~~~~~~~~

With configuration: ``['format' => 'short', 'shorten_simple_statements_only' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?=1?>
   -<?php print '2' . '3'; ?>
   -<?php /* comment */ echo '2' . '3'; ?>
   -<?php print '2' . '3'; someFunction(); ?>
   +<?= '2' . '3'; ?>
   +<?=/* comment */ '2' . '3'; ?>
   +<?= '2' . '3'; someFunction(); ?>

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``echo_tag_syntax`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``echo_tag_syntax`` rule with the default config.
