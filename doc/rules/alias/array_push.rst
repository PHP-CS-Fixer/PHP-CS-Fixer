===================
Rule ``array_push``
===================

Converts simple usages of ``array_push($x, $y);`` to ``$x[] = $y;``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the function ``array_push`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -array_push($x, $y);
   +$x[] = $y;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\ArrayPushFixer <./../../../src/Fixer/Alias/ArrayPushFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\ArrayPushFixerTest <./../../../tests/Fixer/Alias/ArrayPushFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
