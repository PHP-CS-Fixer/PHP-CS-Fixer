=====================
Rule ``ereg_to_preg``
=====================

Replace deprecated ``ereg`` regular expression functions with ``preg``.

.. warning:: Using this rule is risky.

   Risky if the ``ereg`` function is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $x = ereg('[A-Z]');
   +<?php $x = preg_match('/[A-Z]/D');

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``ereg_to_preg`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``ereg_to_preg`` rule.
