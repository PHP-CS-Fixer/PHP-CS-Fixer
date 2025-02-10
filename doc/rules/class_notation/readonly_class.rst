=======================
Rule ``readonly_class``
=======================

Removes redundant ``readonly`` from properties where possible and adds
``readonly`` modifier  if the class is final.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Changing ``readonly`` properties might cause code execution to break.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
                readonly class MyService
                {
   -                private readonly Foo $foo;
   +                private Foo $foo;

                    public function __construct(
                        FooFactory $fooFactory,
   -                    private readonly Bar $bar,
   +                    private Bar $bar,
                    ) {
                        $this->foo = $fooFactory->create();
                    }
                }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -            final class TestClass
   +            final readonly class TestClass
                {
                    public function __construct(
   -                     public readonly string $foo,
   -                     public readonly int $bar,
   +                     public string $foo,
   +                     public int $bar,
                    ) {}
                }
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\ReadonlyClassFixer <./../../../src/Fixer/ClassNotation/ReadonlyClassFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ReadonlyClassFixerTest <./../../../tests/Fixer/ClassNotation/ReadonlyClassFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
