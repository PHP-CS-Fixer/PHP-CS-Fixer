=========================
Rule ``modernize_strpos``
=========================

Replace ``strpos()`` expressions with ``str_starts_with()`` or
``str_contains()`` if possible.

.. warning:: Using this rule is risky.

   Risky if the ``strpos`` function is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php if (strpos($haystack, $needle) === 0) {}
   +<?php if (str_starts_with($haystack, $needle)) {}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php if (strpos($haystack, $needle) !== 0) {}
   +<?php if (!str_starts_with($haystack, $needle)) {}

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php if (strpos($haystack, $needle) !== false) {}
   +<?php if (str_contains($haystack, $needle)) {}

Example #4
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php if (strpos($haystack, $needle) === false) {}
   +<?php if (!str_contains($haystack, $needle)) {}

Rule sets
---------

The rule is part of the following rule set:

@PHP80Migration:risky
  Using the `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ rule set will enable the ``modernize_strpos`` rule.
