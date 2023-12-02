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
    <?php
   -$sample = $b [ 'a' ] [ 'b' ];
   +$sample = $b['a']['b'];

Example #2
~~~~~~~~~~

With configuration: ``['positions' => ['inside']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = $b [ 'a' ] [ 'b' ];
   +$sample = $b ['a'] ['b'];

Example #3
~~~~~~~~~~

With configuration: ``['positions' => ['outside']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = $b [ 'a' ] [ 'b' ];
   +$sample = $b[ 'a' ][ 'b' ];

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Whitespace\\NoSpacesAroundOffsetFixer <./../../../src/Fixer/Whitespace/NoSpacesAroundOffsetFixer.php>`_
