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

Examples
--------

Example #1
~~~~~~~~~~

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

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /** @param string|null $bar */
   -function my_foo($bar)
   +function my_foo(?string $bar)
    {}
