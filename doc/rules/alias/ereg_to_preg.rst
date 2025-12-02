=====================
Rule ``ereg_to_preg``
=====================

Replace deprecated ``ereg`` regular expression functions with ``preg``.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\EregToPregFixer <./../../../src/Fixer/Alias/EregToPregFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\EregToPregFixerTest <./../../../tests/Fixer/Alias/EregToPregFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
