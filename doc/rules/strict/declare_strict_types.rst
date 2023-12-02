=============================
Rule ``declare_strict_types``
=============================

Force strict types declaration in all files. Requires PHP >= 7.0.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Forcing strict types will stop non strict code from working.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php
   +<?php declare(strict_types=1);
   \ No newline at end of file

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_
- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Strict\\DeclareStrictTypesFixer <./../../../src/Fixer/Strict/DeclareStrictTypesFixer.php>`_
