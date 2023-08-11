=====================================
Rule ``fully_qualified_strict_types``
=====================================

Transforms imported FQCN parameters and return types in function arguments to
short version.

Configuration
-------------

``no_namespace_backslash``
~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether FQCN is prefixed with backslash even when in no/global namespace.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    use Foo\Bar;
    use Foo\Bar\Baz;

    class SomeClass
    {
   -    public function doX(\Foo\Bar $foo): \Foo\Bar\Baz
   +    public function doX(Bar $foo): Baz
        {
        }

   -    public function doY(Foo\NotImported $u, \Foo\NotImported $v)
   +    public function doY(\Foo\NotImported $u, \Foo\NotImported $v)
        {
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['no_namespace_backslash' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    class SomeClass
    {
   -    public function doY(Foo\NotImported $u, \Foo\NotImported $v)
   +    public function doY(Foo\NotImported $u, Foo\NotImported $v)
        {
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

