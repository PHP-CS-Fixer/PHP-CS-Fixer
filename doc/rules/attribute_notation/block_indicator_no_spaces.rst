==================================
Rule ``block_indicator_no_spaces``
==================================

Remove spaces before and after the attributes block.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class User
    {
   -    #[
   -        ApiProperty(identifier: true)
   -    ]
   +    #[ApiProperty(identifier: true)]
        private string $name;
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\AttributeNotation\\BlockIndicatorNoSpacesFixer <./../../../src/Fixer/AttributeNotation/BlockIndicatorNoSpacesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\BlockIndicatorNoSpacesFixerTest <./../../../tests/Fixer/AttributeNotation/BlockIndicatorNoSpacesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
