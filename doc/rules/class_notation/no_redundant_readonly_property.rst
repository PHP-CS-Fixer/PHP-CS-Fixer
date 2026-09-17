=======================================
Rule ``no_redundant_readonly_property``
=======================================

Removes redundant readonly from properties in readonly classes.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    readonly class Foo
    {
   -    private readonly int $bar;
   +    private int $bar;
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\NoRedundantReadonlyPropertyFixer <./../../../src/Fixer/ClassNotation/NoRedundantReadonlyPropertyFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\NoRedundantReadonlyPropertyFixerTest <./../../../tests/Fixer/ClassNotation/NoRedundantReadonlyPropertyFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
