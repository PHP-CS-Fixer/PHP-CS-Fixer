=======================
Rule ``ordered_traits``
=======================

Trait ``use`` statements must be sorted alphabetically.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when depending on order of the imports.

Configuration
-------------

``order``
~~~~~~~~~

How the traits should be ordered.

Allowed values: ``'alpha'``, ``'length'``

Default value: ``'alpha'``

``direction``
~~~~~~~~~~~~~

Which direction the traits should be ordered by.

Allowed values: ``'ascend'``, ``'descend'``

Default value: ``'ascend'``

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Whether the sorting should be case sensitive.

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
    <?php class Foo {
   -use Z; use A; }
   +use A; use Z; }

Example #2
~~~~~~~~~~

With configuration: ``['order' => 'length']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php class Foo {
   -use Aaa; use A; use Aa; }
   +use A; use Aa; use Aaa; }

Example #3
~~~~~~~~~~

With configuration: ``['order' => 'length', 'direction' => 'descend']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php class Foo {
   -use Aaa; use A; use Aa; }
   +use Aaa; use Aa; use A; }

Example #4
~~~~~~~~~~

With configuration: ``['case_sensitive' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php class Foo {
   -use Aaa; use AA; }
   +use AA; use Aaa; }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

