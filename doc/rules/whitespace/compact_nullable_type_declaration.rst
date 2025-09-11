==========================================
Rule ``compact_nullable_type_declaration``
==========================================

Remove extra spaces in a nullable type declaration.

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

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\CompactNullableTypeDeclarationFixer <./../../../src/Fixer/Whitespace/CompactNullableTypeDeclarationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\CompactNullableTypeDeclarationFixerTest <./../../../tests/Fixer/Whitespace/CompactNullableTypeDeclarationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
