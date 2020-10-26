==============================================
Rule ``no_unreachable_default_argument_value``
==============================================

In function arguments there must not be arguments with default values before
non-default ones.

.. warning:: Using this rule is risky.

   Modifies the signature of functions; therefore risky when using systems (such
   as some Symfony components) that rely on those (for example through
   reflection).

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -function example($foo = "two words", $bar) {}
   +function example($foo, $bar) {}

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``no_unreachable_default_argument_value`` rule.
