=======================
Rule ``lowercase_cast``
=======================

Cast should be written in lower case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -    $a = (BOOLEAN) $b;
   -    $a = (BOOL) $b;
   -    $a = (INTEGER) $b;
   -    $a = (INT) $b;
   -    $a = (DOUBLE) $b;
   -    $a = (FLoaT) $b;
   -    $a = (flOAT) $b;
   -    $a = (sTRING) $b;
   -    $a = (ARRAy) $b;
   -    $a = (OBJect) $b;
   -    $a = (UNset) $b;
   -    $a = (Binary) $b;
   +    $a = (boolean) $b;
   +    $a = (bool) $b;
   +    $a = (integer) $b;
   +    $a = (int) $b;
   +    $a = (double) $b;
   +    $a = (float) $b;
   +    $a = (float) $b;
   +    $a = (string) $b;
   +    $a = (array) $b;
   +    $a = (object) $b;
   +    $a = (unset) $b;
   +    $a = (binary) $b;

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\CastNotation\\LowercaseCastFixer <./../../../src/Fixer/CastNotation/LowercaseCastFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\CastNotation\\LowercaseCastFixerTest <./../../../tests/Fixer/CastNotation/LowercaseCastFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
