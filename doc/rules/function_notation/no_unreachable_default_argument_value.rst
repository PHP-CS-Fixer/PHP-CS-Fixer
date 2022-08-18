==============================================
Rule ``no_unreachable_default_argument_value``
==============================================

In function arguments there must not be arguments with default values before
non-default ones.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Modifies the signature of functions; therefore risky when using systems (such as
some Symfony components) that rely on those (for example through reflection).

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function example($foo = "two words", $bar) {}
   +function example($foo, $bar) {}

Rule sets
---------

The rule is part of the following rule sets:

@PER:risky
  Using the `@PER:risky <./../../ruleSets/PERRisky.rst>`_ rule set will enable the ``no_unreachable_default_argument_value`` rule.

@PHP80Migration:risky
  Using the `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ rule set will enable the ``no_unreachable_default_argument_value`` rule.

@PSR12:risky
  Using the `@PSR12:risky <./../../ruleSets/PSR12Risky.rst>`_ rule set will enable the ``no_unreachable_default_argument_value`` rule.

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``no_unreachable_default_argument_value`` rule.
