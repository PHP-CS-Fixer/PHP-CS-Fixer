==================================
Rule ``compact_nullable_typehint``
==================================

Remove extra spaces in a nullable typehint.

Warning
-------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``compact_nullable_type_declaration`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(? string $str): ? string
   +function sample(?string $str): ?string
    {}

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\CompactNullableTypehintFixer <./../../../src/Fixer/Whitespace/CompactNullableTypehintFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\CompactNullableTypehintFixerTest <./../../../tests/Fixer/Whitespace/CompactNullableTypehintFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
