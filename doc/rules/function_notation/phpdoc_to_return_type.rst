==============================
Rule ``phpdoc_to_return_type``
==============================

EXPERIMENTAL: Takes ``@return`` annotation of non-mixed types and adjusts
accordingly the function signature. Requires PHP >= 7.0.

.. warning:: Using this rule is risky.

   This rule is EXPERIMENTAL and [1] is not covered with backward compatibility
   promise. [2] ``@return`` annotation is mandatory for the fixer to make
   changes, signatures of methods without it (no docblock, inheritdocs) will not
   be fixed. [3] Manual actions are required if inherited signatures are not
   properly documented. [4] ``@inheritdocs`` support is under construction.

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
   @@ -1,5 +1,5 @@
    <?php

    /** @return \My\Bar */
   -function my_foo()
   +function my_foo(): \My\Bar
    {}

Example #2
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php

    /** @return void */
   -function my_foo()
   +function my_foo(): void
    {}

Example #3
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php

    /** @return object */
   -function my_foo()
   +function my_foo(): object
    {}

Example #4
~~~~~~~~~~

With configuration: ``['scalar_types' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    /** @return Foo */
   -function foo() {}
   +function foo(): Foo {}
    /** @return string */
    function bar() {}

Example #5
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,7 +3,7 @@
        /**
         * @return static
         */
   -    public function create($prototype) {
   +    public function create($prototype): static {
            return new static($prototype);
        }
    }
