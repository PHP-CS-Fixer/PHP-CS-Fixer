=============================
Rule ``return_to_yield_from``
=============================

If the function explicitly returns an array, and has the return type
``iterable``, then ``yield from`` must be used instead of ``return``.

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
Source class
------------

`PhpCsFixer\\Fixer\\ArrayNotation\\ReturnToYieldFromFixer <./../../../src/Fixer/ArrayNotation/ReturnToYieldFromFixer.php>`_
