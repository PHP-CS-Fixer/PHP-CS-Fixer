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
   @@ -4,7 +4,7 @@

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
   @@ -5,7 +5,7 @@

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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``fully_qualified_strict_types`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``fully_qualified_strict_types`` rule.
