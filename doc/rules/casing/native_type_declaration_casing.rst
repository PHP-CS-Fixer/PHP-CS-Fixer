=======================================
Rule ``native_type_declaration_casing``
=======================================

Native type declarations should be used in the correct case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Bar {
   -    public function Foo(CALLABLE $bar): INT
   +    public function Foo(callable $bar): int
        {
            return 1;
        }
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo
    {
   -    const INT BAR = 1;
   +    const int BAR = 1;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Casing\\NativeTypeDeclarationCasingFixer <./../../../src/Fixer/Casing/NativeTypeDeclarationCasingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Casing\\NativeTypeDeclarationCasingFixerTest <./../../../tests/Fixer/Casing/NativeTypeDeclarationCasingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
