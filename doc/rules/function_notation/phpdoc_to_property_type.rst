================================
Rule ``phpdoc_to_property_type``
================================

EXPERIMENTAL: Takes ``@var`` annotation of non-mixed types and adjusts
accordingly the property signature. Requires PHP >= 7.4.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

This rule is EXPERIMENTAL and [1] is not covered with backward compatibility
promise. [2] ``@var`` annotation is mandatory for the fixer to make changes,
signatures of properties without it (no docblock) will not be fixed. [3] Manual
actions might be required for newly typed properties that are read before
initialization.

Configuration
-------------

``scalar_types``
~~~~~~~~~~~~~~~~

Fix also scalar types; may have unexpected behaviour due to PHP bad type
coercion system.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
        /** @var int */
   -    private $foo;
   +    private int $foo;
        /** @var \Traversable */
   -    private $bar;
   +    private \Traversable $bar;
    }

Example #2
~~~~~~~~~~

With configuration: ``['scalar_types' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
        /** @var int */
        private $foo;
        /** @var \Traversable */
   -    private $bar;
   +    private \Traversable $bar;
    }
