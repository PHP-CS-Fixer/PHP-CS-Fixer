=========================
Rule ``set_type_to_cast``
=========================

Cast shall be used, not ``settype``.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when the ``settype`` function is overridden or when used as the 2nd or 3rd
expression in a ``for`` loop .

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -settype($foo, "integer");
   -settype($bar, "string");
   -settype($bar, "null");
   +$foo = (int) $foo;
   +$bar = (string) $bar;
   +$bar = null;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\SetTypeToCastFixer <./../../../src/Fixer/Alias/SetTypeToCastFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\SetTypeToCastFixerTest <./../../../tests/Fixer/Alias/SetTypeToCastFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
