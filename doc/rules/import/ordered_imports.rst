========================
Rule ``ordered_imports``
========================

Ordering ``use`` statements.

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

Allowed types: ``array`` and ``null``

Default value: ``null``

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

Whether the statements should be sorted alphabetically or by length, or not
sorted.

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

- `@PER <./../../ruleSets/PER.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha']``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha']``


Source class
------------

`PhpCsFixer\\Fixer\\Import\\OrderedImportsFixer <./../src/Fixer/Import/OrderedImportsFixer.php>`_
