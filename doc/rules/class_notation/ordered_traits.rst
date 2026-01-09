=======================
Rule ``ordered_traits``
=======================

Trait ``use`` statements must be sorted alphabetically.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when depending on order of the imports.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``case_sensitive``.

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\OrderedTraitsFixer <./../../../src/Fixer/ClassNotation/OrderedTraitsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\OrderedTraitsFixerTest <./../../../tests/Fixer/ClassNotation/OrderedTraitsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
