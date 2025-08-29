===========================
Rule ``ordered_attributes``
===========================

Sorts attributes using the configured sort algorithm.

Configuration
-------------

``order``
~~~~~~~~~

A list of FQCNs of attributes defining the desired order used when custom
sorting algorithm is configured.

Allowed types: ``list<string>``

Default value: ``[]``

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

How the attributes should be sorted.

Allowed values: ``'alpha'`` and ``'custom'``

Default value: ``'alpha'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   +#[Bar(3)]
   +#[Corge(a: 'test')]
    #[Foo]
   -#[Bar(3)]
    #[Qux(new Bar(5))]
   -#[Corge(a: 'test')]
    class Sample1 {}

    #[
   +    Bar(3),
   +    Corge(a: 'test'),
        Foo,
   -    Bar(3),
        Qux(new Bar(5)),
   -    Corge(a: 'test'),
    ]
    class Sample2 {}

Example #2
~~~~~~~~~~

With configuration: ``['sort_algorithm' => 'custom', 'order' => ['A\\B\\Qux', 'A\\B\\Bar', 'A\\B\\Corge']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    use A\B\Foo;
    use A\B\Bar as BarAlias;
    use A\B as AB;

   -#[Foo]
   +#[AB\Qux(new Bar(5))]
    #[BarAlias(3)]
   -#[AB\Qux(new Bar(5))]
    #[\A\B\Corge(a: 'test')]
   +#[Foo]
    class Sample1 {}

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\AttributeNotation\\OrderedAttributesFixer <./../../../src/Fixer/AttributeNotation/OrderedAttributesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\OrderedAttributesFixerTest <./../../../tests/Fixer/AttributeNotation/OrderedAttributesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
