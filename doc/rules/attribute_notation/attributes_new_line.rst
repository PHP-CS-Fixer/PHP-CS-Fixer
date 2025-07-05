============================
Rule ``attributes_new_line``
============================

Attributes should be on their own line.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -#[Foo] #[Bar] class Baz
   +#[Foo]
   +#[Bar]
   +class Baz
    {
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -#[Foo] class Bar
   +#[Foo]
   +class Bar
    {
   -    #[Baz] public function foo() {}
   +    #[Baz]
   +    public function foo() {}
    }

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -#[Foo] class Bar
   +#[Foo]
   +class Bar
    {
   -    #[Test] public const TEST = 'Test';
   +    #[Test]
   +    public const TEST = 'Test';
    }
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\AttributeNotation\\AttributesNewLineFixer <./../../../src/Fixer/AttributeNotation/AttributesNewLineFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\AttributesNewLineFixerTest <./../../../tests/Fixer/AttributeNotation/AttributesNewLineFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
