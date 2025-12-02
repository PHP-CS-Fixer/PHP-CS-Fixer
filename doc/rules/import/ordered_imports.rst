========================
Rule ``ordered_imports``
========================

Ordering ``use`` statements.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``case_sensitive``,
``imports_order``, ``sort_algorithm``.

Configuration
-------------

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Whether the sorting should be case sensitive.

Allowed types: ``bool``

Default value: ``false``

``imports_order``
~~~~~~~~~~~~~~~~~

Defines the order of import types.

Allowed types: ``list<string>`` and ``null``

Default value: ``null``

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

Whether the statements should be sorted alphabetically or by length
(*deprecated*), or not sorted.

Allowed values: ``'alpha'``, ``'length'`` and ``'none'``

Default value: ``'alpha'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +use AAA;
   +use const AAB;
    use function AAC;
   -use const AAB;
   -use AAA;

Example #2
~~~~~~~~~~

With configuration: ``['case_sensitive' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +use const AA;
    use function Aaa;
   -use const AA;

Example #3
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'length']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +use Bar;
   +use Acme;
   +use Bar1;
    use Acme\Bar;
   -use Bar1;
   -use Acme;
   -use Bar;

Example #4
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'length', 'imports_order' => ['const', 'class', 'function']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +use const BBB;
    use const AAAA;
   -use const BBB;

   +use AAC;
    use Bar;
   -use AAC;
    use Acme;

   +use function DDD;
    use function CCC\AA;
   -use function DDD;

Example #5
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'alpha', 'imports_order' => ['const', 'class', 'function']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +use const AAAA;
    use const BBB;
   -use const AAAA;

   +use AAC;
    use Acme;
   -use AAC;
    use Bar;

   +use function CCC\AA;
    use function DDD;
   -use function CCC\AA;

Example #6
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'none', 'imports_order' => ['const', 'class', 'function']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use const BBB;
    use const AAAA;

   -use function DDD;
   -use function CCC\AA;
   -
    use Acme;
    use AAC;
   +
    use Bar;
   +use function DDD;
   +use function CCC\AA;

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)* with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ *(deprecated)* with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)* with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)* with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha']``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\OrderedImportsFixer <./../../../src/Fixer/Import/OrderedImportsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\OrderedImportsFixerTest <./../../../tests/Fixer/Import/OrderedImportsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
