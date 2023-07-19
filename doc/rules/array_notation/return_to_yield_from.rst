=============================
Rule ``return_to_yield_from``
=============================

When function return type is iterable and it starts with ``return`` then it must
be changed to ``yield from``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php function giveMeData(): iterable {
   -    return [1, 2, 3];
   +    yield from [1, 2, 3];
    }
