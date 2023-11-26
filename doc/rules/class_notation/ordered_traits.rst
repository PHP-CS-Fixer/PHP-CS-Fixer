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

Source class
------------

`PhpCsFixer\\Fixer\\ClassNotation\\OrderedTraitsFixer <./../src/Fixer/ClassNotation/OrderedTraitsFixer.php>`_
