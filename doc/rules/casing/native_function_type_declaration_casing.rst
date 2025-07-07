================================================
Rule ``native_function_type_declaration_casing``
================================================

Native type declarations for functions should use the correct case.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``native_type_declaration_casing`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Bar {
   -    public function Foo(CALLABLE $bar)
   +    public function Foo(callable $bar)
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
   -function Foo(INT $a): Bool
   +function Foo(int $a): bool
    {
        return true;
    }

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function Foo(Iterable $a): VOID
   +function Foo(iterable $a): void
    {
        echo 'Hello world';
    }

Example #4
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function Foo(Object $a)
   +function Foo(object $a)
    {
        return 'hi!';
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Casing\\NativeFunctionTypeDeclarationCasingFixer <./../../../src/Fixer/Casing/NativeFunctionTypeDeclarationCasingFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Casing\\NativeFunctionTypeDeclarationCasingFixerTest <./../../../tests/Fixer/Casing/NativeFunctionTypeDeclarationCasingFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
