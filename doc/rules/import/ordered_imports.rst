========================
Rule ``ordered_imports``
========================

Ordering ``use`` statements.

Configuration
-------------

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

whether the statements should be sorted alphabetically or by length, or not
sorted

Allowed values: ``'alpha'``, ``'length'``, ``'none'``

Default value: ``'alpha'``

``imports_order``
~~~~~~~~~~~~~~~~~

Defines the order of import types.

Allowed types: ``array``, ``null``

Default value: ``null``

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

Example #3
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

Example #4
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

Example #5
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

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``ordered_imports`` rule with the config below:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``ordered_imports`` rule with the config below:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``ordered_imports`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``ordered_imports`` rule with the default config.
