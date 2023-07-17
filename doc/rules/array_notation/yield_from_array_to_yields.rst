===================================
Rule ``yield_from_array_to_yields``
===================================

Yield from array must be unpacked to series of yields.

Description
-----------

The conversion will make the array in ``yield from`` changed in arrays of 1 less
dimension.

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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``yield_from_array_to_yields`` rule.
