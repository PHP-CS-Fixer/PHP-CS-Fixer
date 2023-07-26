=====================
Rule ``implode_call``
=====================

Function ``implode`` must be called with 2 arguments in the documented order.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the function ``implode`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -implode($pieces, '');
   +implode('', $pieces);

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -implode($pieces);
   +implode('', $pieces);

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP74Migration:risky <./../../ruleSets/PHP74MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

