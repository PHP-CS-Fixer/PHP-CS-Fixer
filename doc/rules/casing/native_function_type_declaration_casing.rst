================================================
Rule ``native_function_type_declaration_casing``
================================================

Native type hints for functions should use the correct case.

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

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

