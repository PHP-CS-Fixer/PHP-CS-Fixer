===================================
Rule ``yield_from_array_to_yields``
===================================

Yield from array must be unpacked to series of yields.

Description
-----------

The conversion will make the array in ``yield from`` changed in arrays of 1 less
dimension.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

The rule is risky in case of ``yield from`` being used multiple times within
single function scope, while using list-alike data sources (e.g. ``function
foo() { yield from ["a"]; yield from ["b"]; }``). It only matters when consuming
such iterator with key-value context, because set of yielded keys may be changed
after applying this rule.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php function generate() {
   -    yield from [
   -        1,
   -        2,
   -        3,
   -    ];
   +     
   +        yield 1;
   +        yield 2;
   +        yield 3;
   +    
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\YieldFromArrayToYieldsFixer <./../../../src/Fixer/ArrayNotation/YieldFromArrayToYieldsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\YieldFromArrayToYieldsFixerTest <./../../../tests/Fixer/ArrayNotation/YieldFromArrayToYieldsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
