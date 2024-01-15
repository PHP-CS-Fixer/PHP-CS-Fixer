================================
Rule ``phpdoc_to_property_type``
================================

Takes ``@var`` annotation of non-mixed types and adjusts accordingly the
property signature. Requires PHP >= 7.4.

Warning
-------

This rule is experimental
~~~~~~~~~~~~~~~~~~~~~~~~~

Rule is not covered with backward compatibility promise, use it at your own
risk. Rule's behaviour may be changed at any point, including rule's name; its
options' names, availability and allowed values; its default configuration. Rule
may be even removed without prior notice. Feel free to provide feedback and help
with determining final state of the rule.

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

The ``@var`` annotation is mandatory for the fixer to make changes, signatures
of properties without it (no docblock) will not be fixed. Manual actions might
be required for newly typed properties that are read before initialization.

Configuration
-------------

``scalar_types``
~~~~~~~~~~~~~~~~

Fix also scalar types; may have unexpected behaviour due to PHP bad type
coercion system.

Allowed types: ``bool``

Default value: ``true``

``union_types``
~~~~~~~~~~~~~~~

Fix also union types; turned on by default on PHP >= 8.0.0.

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

Example #3
~~~~~~~~~~

With configuration: ``['union_types' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
        /** @var int|string */
        private $foo;
        /** @var \Traversable */
   -    private $bar;
   +    private \Traversable $bar;
    }
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\PhpdocToPropertyTypeFixer <./../../../src/Fixer/FunctionNotation/PhpdocToPropertyTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\PhpdocToPropertyTypeFixerTest <./../../../tests/Fixer/FunctionNotation/PhpdocToPropertyTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
