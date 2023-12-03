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

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

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

Source class
------------

`PhpCsFixer\\Fixer\\ArrayNotation\\YieldFromArrayToYieldsFixer <./../../../src/Fixer/ArrayNotation/YieldFromArrayToYieldsFixer.php>`_
