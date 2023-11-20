=====================
Rule ``ereg_to_preg``
=====================

Replace deprecated ``ereg`` regular expression functions with ``preg``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky if the ``ereg`` function is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $x = ereg('[A-Z]');
   +<?php $x = preg_match('/[A-Z]/D');

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Alias\\EregToPregFixer <./../src/Fixer/Alias/EregToPregFixer.php>`_
