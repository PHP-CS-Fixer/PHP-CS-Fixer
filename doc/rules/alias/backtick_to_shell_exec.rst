===============================
Rule ``backtick_to_shell_exec``
===============================

Converts backtick operators to ``shell_exec`` calls.

Description
-----------

Conversion is done only when it is non risky, so when special chars like
single-quotes, double-quotes and backticks are not used inside the command.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
   -$plain = `ls -lah`;
   -$withVar = `ls -lah $var1 ${var2} {$var3} {$var4[0]} {$var5->call()}`;
   +$plain = shell_exec("ls -lah");
   +$withVar = shell_exec("ls -lah $var1 ${var2} {$var3} {$var4[0]} {$var5->call()}");

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``backtick_to_shell_exec`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``backtick_to_shell_exec`` rule.
