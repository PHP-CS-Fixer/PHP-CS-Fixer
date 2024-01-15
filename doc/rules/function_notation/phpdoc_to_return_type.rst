==============================
Rule ``phpdoc_to_return_type``
==============================

Takes ``@return`` annotation of non-mixed types and adjusts accordingly the
function signature.

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

The ``@return`` annotation is mandatory for the fixer to make changes,
signatures of methods without it (no docblock, inheritdocs) will not be fixed.
Manual actions are required if inherited signatures are not properly documented.

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

With configuration: ``['union_types' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /** @return Foo */
   -function foo() {}
   +function foo(): Foo {}
    /** @return int|string */
    function bar() {}

Example #4
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
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\PhpdocToReturnTypeFixer <./../../../src/Fixer/FunctionNotation/PhpdocToReturnTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\PhpdocToReturnTypeFixerTest <./../../../tests/Fixer/FunctionNotation/PhpdocToReturnTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
