=====================================
Rule ``fully_qualified_strict_types``
=====================================

Transforms imported FQCN parameters and return types in function arguments to
short version.

Configuration
-------------

``leading_backslash_in_global_namespace``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether FQCN is prefixed with backslash when that FQCN is used in global
namespace context.

Allowed types: ``bool``

Default value: ``false``

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

    /**
   - * @see \Foo\Bar\Baz
   + * @see Baz
     */
    class SomeClass
    {
        /**
   -     * @var \Foo\Bar\Baz
   +     * @var Baz
         */
        public $baz;

        /**
   -     * @param \Foo\Bar\Baz $baz
   +     * @param Baz $baz
         */
        public function __construct($baz) {
            $this->baz = $baz;
        }

        /**
   -     * @return \Foo\Bar\Baz
   +     * @return Baz
         */
        public function getBaz() {
            return $this->baz;
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['leading_backslash_in_global_namespace' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    class SomeClass
    {
   -    public function doY(Foo\NotImported $u, \Foo\NotImported $v)
   +    public function doY(\Foo\NotImported $u, \Foo\NotImported $v)
        {
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Import\\FullyQualifiedStrictTypesFixer <./../../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php>`_
