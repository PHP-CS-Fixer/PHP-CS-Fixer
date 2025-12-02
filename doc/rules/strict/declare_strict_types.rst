=============================
Rule ``declare_strict_types``
=============================

Force strict types declaration in all files.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Forcing strict types will stop non strict code from working.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option:
``preserve_existing_declaration``.

Configuration
-------------

``preserve_existing_declaration``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether existing strict_types=? should be preserved and not overridden.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php
   +<?php declare(strict_types=1);
   \ No newline at end of file

Example #2
~~~~~~~~~~

With configuration: ``['preserve_existing_declaration' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -declare(Strict_Types=0);
   +declare(strict_types=1);

Example #3
~~~~~~~~~~

With configuration: ``['preserve_existing_declaration' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -declare(Strict_Types=0);
   +declare(strict_types=0);

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP7x0Migration:risky <./../../ruleSets/PHP7x0MigrationRisky.rst>`_
- `@PHP7x1Migration:risky <./../../ruleSets/PHP7x1MigrationRisky.rst>`_
- `@PHP7x4Migration:risky <./../../ruleSets/PHP7x4MigrationRisky.rst>`_
- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_
- `@PHP8x1Migration:risky <./../../ruleSets/PHP8x1MigrationRisky.rst>`_
- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP8x3Migration:risky <./../../ruleSets/PHP8x3MigrationRisky.rst>`_
- `@PHP8x4Migration:risky <./../../ruleSets/PHP8x4MigrationRisky.rst>`_
- `@PHP8x5Migration:risky <./../../ruleSets/PHP8x5MigrationRisky.rst>`_
- `@PHP70Migration:risky <./../../ruleSets/PHP70MigrationRisky.rst>`_ *(deprecated)*
- `@PHP71Migration:risky <./../../ruleSets/PHP71MigrationRisky.rst>`_ *(deprecated)*
- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_ *(deprecated)*
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ *(deprecated)*
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Strict\\DeclareStrictTypesFixer <./../../../src/Fixer/Strict/DeclareStrictTypesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Strict\\DeclareStrictTypesFixerTest <./../../../tests/Fixer/Strict/DeclareStrictTypesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
