==========================
Rule ``logical_operators``
==========================

Use ``&&`` and ``||`` logical operators instead of ``and`` and ``or``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky, because you must double-check if using and/or with lower precedence was
intentional.

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

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Operator\\LogicalOperatorsFixer <./../src/Fixer/Operator/LogicalOperatorsFixer.php>`_
