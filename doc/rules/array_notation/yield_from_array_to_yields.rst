===================================
Rule ``yield_from_array_to_yields``
===================================

Yield from array should be unpacked to series of yields.

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
