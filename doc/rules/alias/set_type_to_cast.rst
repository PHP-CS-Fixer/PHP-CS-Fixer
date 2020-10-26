=========================
Rule ``set_type_to_cast``
=========================

Cast shall be used, not ``settype``.

.. warning:: Using this rule is risky.

   Risky when the ``settype`` function is overridden or when used as the 2nd or
   3rd expression in a ``for`` loop .

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
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

@Symfony:risky
  Using the ``@Symfony:risky`` rule set will enable the ``set_type_to_cast`` rule.

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``set_type_to_cast`` rule.
