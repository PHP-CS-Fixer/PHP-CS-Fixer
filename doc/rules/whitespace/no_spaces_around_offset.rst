================================
Rule ``no_spaces_around_offset``
================================

There MUST NOT be spaces around offset braces.

Configuration
-------------

``positions``
~~~~~~~~~~~~~

Whether spacing should be fixed inside and/or outside the offset braces.

Allowed values: a subset of ``['inside', 'outside']``

Default value: ``['inside', 'outside']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$sample = $b [ 'a' ] [ 'b' ];
   +$sample = $b['a']['b'];

Example #2
~~~~~~~~~~

With configuration: ``['positions' => ['inside']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$sample = $b [ 'a' ] [ 'b' ];
   +$sample = $b ['a'] ['b'];

Example #3
~~~~~~~~~~

With configuration: ``['positions' => ['outside']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$sample = $b [ 'a' ] [ 'b' ];
   +$sample = $b[ 'a' ][ 'b' ];

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_spaces_around_offset`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_spaces_around_offset`` rule with the default config.
