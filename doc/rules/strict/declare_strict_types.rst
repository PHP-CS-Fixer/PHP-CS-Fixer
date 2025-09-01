=============================
Rule ``declare_strict_types``
=============================

Force strict types declaration in all files.

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

- `@PHP7.0Migration:risky <./../../ruleSets/PHP7.0MigrationRisky.rst>`_
- `@PHP7.1Migration:risky <./../../ruleSets/PHP7.1MigrationRisky.rst>`_
- `@PHP7.4Migration:risky <./../../ruleSets/PHP7.4MigrationRisky.rst>`_
- `@PHP8.0Migration:risky <./../../ruleSets/PHP8.0MigrationRisky.rst>`_
- `@PHP8.2Migration:risky <./../../ruleSets/PHP8.2MigrationRisky.rst>`_
- `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_
- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Strict\\DeclareStrictTypesFixer <./../../../src/Fixer/Strict/DeclareStrictTypesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Strict\\DeclareStrictTypesFixerTest <./../../../tests/Fixer/Strict/DeclareStrictTypesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
