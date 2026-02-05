========================================
Rule ``no_unneeded_control_parentheses``
========================================

Removes unneeded parentheses around control statements.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``statements``.

Configuration
-------------

``statements``
~~~~~~~~~~~~~~

List of control statements to fix.

Allowed values: a subset of ``['break', 'clone', 'continue', 'echo_print', 'negative_instanceof', 'others', 'return', 'switch_case', 'yield', 'yield_from']``

Default value: ``['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -while ($x) { while ($y) { break (2); } }
   -clone($a);
   -while ($y) { continue (2); }
   -echo("foo");
   -print("foo");
   -return (1 + 2);
   -switch ($a) { case($x); }
   -yield(2);
   +while ($x) { while ($y) { break 2; } }
   +clone $a;
   +while ($y) { continue 2; }
   +echo "foo";
   +print "foo";
   +return 1 + 2;
   +switch ($a) { case $x; }
   +yield 2;

Example #2
~~~~~~~~~~

With configuration: ``['statements' => ['break', 'continue']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -while ($x) { while ($y) { break (2); } }
   +while ($x) { while ($y) { break 2; } }

    clone($a);

   -while ($y) { continue (2); }
   +while ($y) { continue 2; }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['statements' => ['break', 'clone', 'continue', 'echo_print', 'negative_instanceof', 'others', 'return', 'switch_case', 'yield', 'yield_from']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['statements' => ['break', 'clone', 'continue', 'echo_print', 'negative_instanceof', 'others', 'return', 'switch_case', 'yield', 'yield_from']]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\NoUnneededControlParenthesesFixer <./../../../src/Fixer/ControlStructure/NoUnneededControlParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\NoUnneededControlParenthesesFixerTest <./../../../tests/Fixer/ControlStructure/NoUnneededControlParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
