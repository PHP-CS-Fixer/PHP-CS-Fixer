=============================
Rule ``spaces_around_offset``
=============================

There must be no space, or a single space, inside and outside of offset braces,
as configured.

Description
-----------

Whitespace containing a line break is preserved inside the braces, and outside
of them when ``single`` is configured. Offsets in simple string interpolation
(e.g. ``"$a[1]"``) are never changed.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``spacing``.

Configuration
-------------

``spacing``
~~~~~~~~~~~

Dictionary of ``inside|outside`` => ``none|single|ignore`` values.

Allowed types: ``array<string, string>``

Default value: ``['inside' => 'none', 'outside' => 'none']``

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

With configuration: ``['spacing' => ['inside' => 'single', 'outside' => 'single']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = $b['a']['b'];
   +$sample = $b [ 'a' ] [ 'b' ];

Example #3
~~~~~~~~~~

With configuration: ``['spacing' => ['inside' => 'single']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = $b [ 'a' ] [ 'b' ];
   +$sample = $b[ 'a' ][ 'b' ];

Example #4
~~~~~~~~~~

With configuration: ``['spacing' => ['inside' => 'none', 'outside' => 'ignore']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = $b [ 'a' ] [ 'b' ];
   +$sample = $b ['a'] ['b'];

Example #5
~~~~~~~~~~

With configuration: ``['spacing' => ['inside' => 'ignore', 'outside' => 'none']]``.

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\SpacesAroundOffsetFixer <./../../../src/Fixer/Whitespace/SpacesAroundOffsetFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\SpacesAroundOffsetFixerTest <./../../../tests/Fixer/Whitespace/SpacesAroundOffsetFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
