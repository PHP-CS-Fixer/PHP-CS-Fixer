===============================
Rule ``single_line_empty_body``
===============================

Empty body of class, interface, trait, enum or function must be abbreviated as
``{}`` and placed on the same line as the previous symbol, separated by a single
space.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``classy``,
``constructors_multi_line``, ``constructors_single_line``, ``functions``.

Configuration
-------------

``classy``
~~~~~~~~~~

Whether to fix empty bodies of classes, interfaces, traits and enums.

Allowed types: ``bool``

Default value: ``true``

``constructors_multi_line``
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to fix empty bodies of constructors whose parameters span multiple
lines.

Allowed types: ``bool``

Default value: ``true``

``constructors_single_line``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to fix empty bodies of constructors whose parameters are on a single
line.

Allowed types: ``bool``

Default value: ``true``

``functions``
~~~~~~~~~~~~~

Whether to fix empty bodies of functions and methods other than constructors.

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
    <?php function foo(
        int $x
   -)
   -{
   -}
   +) {}

Example #2
~~~~~~~~~~

With configuration: ``['classy' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo
    {
    }

   -function foo()
   -{
   -}
   +function foo() {}

Example #3
~~~~~~~~~~

With configuration: ``['constructors_single_line' => true, 'constructors_multi_line' => false, 'classy' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo
    {
   -    public function __construct(private int $x)
   -    {
   -    }
   +    public function __construct(private int $x) {}
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)*
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Basic\\SingleLineEmptyBodyFixer <./../../../src/Fixer/Basic/SingleLineEmptyBodyFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Basic\\SingleLineEmptyBodyFixerTest <./../../../tests/Fixer/Basic/SingleLineEmptyBodyFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
