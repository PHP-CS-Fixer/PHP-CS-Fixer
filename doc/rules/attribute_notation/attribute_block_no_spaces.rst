==================================
Rule ``attribute_block_no_spaces``
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

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\AttributeNotation\\AttributeBlockNoSpacesFixer <./../../../src/Fixer/AttributeNotation/AttributeBlockNoSpacesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\AttributeBlockNoSpacesFixerTest <./../../../tests/Fixer/AttributeNotation/AttributeBlockNoSpacesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
