==============================
Rule ``phpdoc_to_return_type``
==============================

EXPERIMENTAL: Takes ``@return`` annotation of non-mixed types and adjusts
accordingly the function signature.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

This rule is EXPERIMENTAL and [1] is not covered with backward compatibility
promise. [2] ``@return`` annotation is mandatory for the fixer to make changes,
signatures of methods without it (no docblock, inheritdocs) will not be fixed.
[3] Manual actions are required if inherited signatures are not properly
documented.

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

    /** @return \My\Bar */
   -function f1()
   +function f1(): \My\Bar
    {}

    /** @return void */
   -function f2()
   +function f2(): void
    {}

    /** @return object */
   -function my_foo()
   +function my_foo(): object
    {}

Example #2
~~~~~~~~~~

With configuration: ``['scalar_types' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /** @return Foo */
   -function foo() {}
   +function foo(): Foo {}
    /** @return string */
    function bar() {}

Example #3
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Foo {
        /**
         * @return static
         */
   -    public function create($prototype) {
   +    public function create($prototype): static {
            return new static($prototype);
        }
    }
Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\PhpdocToReturnTypeFixer <./../../../src/Fixer/FunctionNotation/PhpdocToReturnTypeFixer.php>`_
