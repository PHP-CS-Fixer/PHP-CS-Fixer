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

.. note:: The previous name of this option was ``sortAlgorithm`` but it is now deprecated and will be removed on next major version.

Allowed values: ``'alpha'``, ``'length'``, ``'none'``

Default value: ``'alpha'``

``imports_order``
~~~~~~~~~~~~~~~~~

Defines the order of import types.

.. note:: The previous name of this option was ``importsOrder`` but it is now deprecated and will be removed on next major version.

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
   @@ -1,2 +1,2 @@
    <?php
   -use Z; use A;
   +use A; use Z;

Example #2
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'length']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
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

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
    <?php
   +use AAA;
   +use const AAB;
    use function AAC;
   -use const AAB;
   -use AAA;

Example #4
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'length', 'imports_order' => ['const', 'class', 'function']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,10 +1,10 @@
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
   @@ -1,10 +1,10 @@
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
   @@ -2,9 +2,9 @@
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

@Symfony
  Using the ``@Symfony`` rule set will enable the ``ordered_imports`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``ordered_imports`` rule with the default config.
