=============================
Rule ``declare_strict_types``
=============================

Force strict types declaration in all files. Requires PHP >= 7.0.

.. warning:: Using this rule is risky.

   Forcing strict types will stop non strict code from working.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php
   +<?php declare(strict_types=1);
   \ No newline at end of file

Rule sets
---------

The rule is part of the following rule sets:

@PHP70Migration:risky
  Using the `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_ rule set will enable the ``declare_strict_types`` rule.

@PHP71Migration:risky
  Using the `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ rule set will enable the ``declare_strict_types`` rule.

@PHP80Migration:risky
  Using the `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ rule set will enable the ``declare_strict_types`` rule.
