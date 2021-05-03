=============================
Rule ``phpdoc_to_param_type``
=============================

EXPERIMENTAL: Takes ``@param`` annotations of non-mixed types and adjusts
accordingly the function signature. Requires PHP >= 7.0.

.. warning:: Using this rule is risky.

   This rule is EXPERIMENTAL and [1] is not covered with backward compatibility
   promise. [2] ``@param`` annotation is mandatory for the fixer to make
   changes, signatures of methods without it (no docblock, inheritdocs) will not
   be fixed. [3] Manual actions are required if inherited signatures are not
   properly documented.

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

    /** @param string $bar */
   -function my_foo($bar)
   +function my_foo(string $bar)
    {}

Example #2
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /** @param string|null $bar */
   -function my_foo($bar)
   +function my_foo(?string $bar)
    {}

Example #3
~~~~~~~~~~

With configuration: ``['scalar_types' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /** @param Foo $foo */
   -function foo($foo) {}
   +function foo(Foo $foo) {}
    /** @param string $foo */
    function bar($foo) {}
