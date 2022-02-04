=========================
Rule ``set_type_to_cast``
=========================

Cast shall be used, not ``settype``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

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

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``set_type_to_cast`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``set_type_to_cast`` rule.
