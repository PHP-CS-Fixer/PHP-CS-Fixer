=============================
Rule ``random_api_migration``
=============================

Replaces ``rand``, ``srand``, ``getrandmax`` functions calls with their ``mt_*``
analogs or ``random_int``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the configured functions are overridden. Or when relying on the seed
based generating of the numbers.

Configuration
-------------

``replacements``
~~~~~~~~~~~~~~~~

Mapping between replaced functions with the new ones.

Allowed types: ``array<string, string>``

Default value: ``['getrandmax' => 'mt_getrandmax', 'rand' => 'mt_rand', 'srand' => 'mt_srand']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = getrandmax();
   -$a = rand($b, $c);
   -$a = srand();
   +$a = mt_getrandmax();
   +$a = mt_rand($b, $c);
   +$a = mt_srand();

Example #2
~~~~~~~~~~

With configuration: ``['replacements' => ['getrandmax' => 'mt_getrandmax']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = getrandmax();
   +$a = mt_getrandmax();
    $a = rand($b, $c);
    $a = srand();

Example #3
~~~~~~~~~~

With configuration: ``['replacements' => ['rand' => 'random_int']]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = rand($b, $c);
   +<?php $a = random_int($b, $c);

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP7x0Migration:risky <./../../ruleSets/PHP7x0MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

- `@PHP7x1Migration:risky <./../../ruleSets/PHP7x1MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

- `@PHP7x4Migration:risky <./../../ruleSets/PHP7x4MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

- `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\RandomApiMigrationFixer <./../../../src/Fixer/Alias/RandomApiMigrationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\RandomApiMigrationFixerTest <./../../../tests/Fixer/Alias/RandomApiMigrationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
