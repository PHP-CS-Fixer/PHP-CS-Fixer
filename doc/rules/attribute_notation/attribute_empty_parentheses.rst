====================================
Rule ``attribute_empty_parentheses``
====================================

PHP attributes declared without arguments must (not) be followed by empty
parentheses.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``use_parentheses``.

Configuration
-------------

``use_parentheses``
~~~~~~~~~~~~~~~~~~~

Whether attributes should be followed by parentheses or not.

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

   -#[Foo()]
   +#[Foo]
    class Sample1 {}

   -#[Bar(), Baz()]
   +#[Bar, Baz]
    class Sample2 {}

Example #2
~~~~~~~~~~

With configuration: ``['use_parentheses' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -#[Foo]
   +#[Foo()]
    class Sample1 {}

   -#[Bar, Baz]
   +#[Bar(), Baz()]
    class Sample2 {}

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\AttributeNotation\\AttributeEmptyParenthesesFixer <./../../../src/Fixer/AttributeNotation/AttributeEmptyParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\AttributeEmptyParenthesesFixerTest <./../../../tests/Fixer/AttributeNotation/AttributeEmptyParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
