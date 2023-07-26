===============================
Rule ``string_length_to_empty``
===============================

String tests for empty must be done against ``''``, not with ``strlen``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when ``strlen`` is overridden, when called using a ``stringable`` object,
also no longer triggers warning when called using non-string(able).

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = 0 === strlen($b) || \STRLEN($c) < 1;
   +<?php $a = '' === $b || $c === '';

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

