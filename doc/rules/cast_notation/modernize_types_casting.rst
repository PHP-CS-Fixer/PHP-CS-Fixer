================================
Rule ``modernize_types_casting``
================================

Replaces ``intval``, ``floatval``, ``doubleval``, ``strval`` and ``boolval``
function calls with according type casting operator.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky if any of the functions ``intval``, ``floatval``, ``doubleval``,
``strval`` or ``boolval`` are overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -    $a = intval($b);
   -    $a = floatval($b);
   -    $a = doubleval($b);
   -    $a = strval ($b);
   -    $a = boolval($b);
   +    $a = (int) $b;
   +    $a = (float) $b;
   +    $a = (float) $b;
   +    $a = (string) $b;
   +    $a = (bool) $b;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\CastNotation\\ModernizeTypesCastingFixer <./../../../src/Fixer/CastNotation/ModernizeTypesCastingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\CastNotation\\ModernizeTypesCastingFixerTest <./../../../tests/Fixer/CastNotation/ModernizeTypesCastingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
