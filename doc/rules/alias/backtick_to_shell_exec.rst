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
    <?php
   -$plain = `ls -lah`;
   -$withVar = `ls -lah $var1 ${var2} {$var3} {$var4[0]} {$var5->call()}`;
   +$plain = shell_exec("ls -lah");
   +$withVar = shell_exec("ls -lah $var1 ${var2} {$var3} {$var4[0]} {$var5->call()}");

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\BacktickToShellExecFixer <./../../../src/Fixer/Alias/BacktickToShellExecFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\BacktickToShellExecFixerTest <./../../../tests/Fixer/Alias/BacktickToShellExecFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
