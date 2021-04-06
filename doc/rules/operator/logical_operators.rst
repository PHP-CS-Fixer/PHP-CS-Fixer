==========================
Rule ``logical_operators``
==========================

Use ``&&`` and ``||`` logical operators instead of ``and`` and ``or``.

.. warning:: Using this rule is risky.

   Risky, because you must double-check if using and/or with lower precedence
   was intentional.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -if ($a == "foo" and ($b == "bar" or $c == "baz")) {
   +if ($a == "foo" && ($b == "bar" || $c == "baz")) {
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``logical_operators`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``logical_operators`` rule.
