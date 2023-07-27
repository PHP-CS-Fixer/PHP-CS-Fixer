=====================================
Rule ``fully_qualified_strict_types``
=====================================

Transforms imported FQCN parameters and return types in function arguments to
short version.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    use Foo\Bar;

    class SomeClass
    {
   -    public function doSomething(\Foo\Bar $foo)
   +    public function doSomething(Bar $foo)
        {
        }
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    use Foo\Bar;
    use Foo\Bar\Baz;

    class SomeClass
    {
   -    public function doSomething(\Foo\Bar $foo): \Foo\Bar\Baz
   +    public function doSomething(Bar $foo): Baz
        {
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

