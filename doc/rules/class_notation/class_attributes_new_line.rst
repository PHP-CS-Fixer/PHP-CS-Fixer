==================================
Rule ``class_attributes_new_line``
==================================

Class attributes should be on their own line.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -#[Foo] #[Bar] class Baz
   +#[Foo]
   +#[Bar]
   +class Baz
    {
    }
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\ClassAttributesNewLineFixer <./../../../src/Fixer/ClassNotation/ClassAttributesNewLineFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ClassAttributesNewLineFixerTest <./../../../tests/Fixer/ClassNotation/ClassAttributesNewLineFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
