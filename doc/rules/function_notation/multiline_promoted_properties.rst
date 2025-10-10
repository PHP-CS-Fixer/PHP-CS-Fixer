======================================
Rule ``multiline_promoted_properties``
======================================

Promoted properties must be on separate lines.

Configuration
-------------

``keep_blank_lines``
~~~~~~~~~~~~~~~~~~~~

Whether to keep blank lines between properties.

Allowed types: ``bool``

Default value: ``false``

``minimum_number_of_parameters``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Minimum number of parameters in the constructor to fix.

Allowed types: ``int``

Default value: ``1``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
   -    public function __construct(private array $a, private bool $b, private int $i) {}
   +    public function __construct(
   +        private array $a,
   +        private bool $b,
   +        private int $i
   +    ) {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['minimum_number_of_parameters' => 3]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
   -    public function __construct(private array $a, private bool $b, private int $i) {}
   +    public function __construct(
   +        private array $a,
   +        private bool $b,
   +        private int $i
   +    ) {}
    }
    class Bar {
        public function __construct(private array $x) {}
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\MultilinePromotedPropertiesFixer <./../../../src/Fixer/FunctionNotation/MultilinePromotedPropertiesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\MultilinePromotedPropertiesFixerTest <./../../../tests/Fixer/FunctionNotation/MultilinePromotedPropertiesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
