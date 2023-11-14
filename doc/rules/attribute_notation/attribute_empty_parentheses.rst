====================================
Rule ``attribute_empty_parentheses``
====================================

PHP attributes declared without arguments must (not) be followed by empty
parentheses.

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
Source class
------------

`PhpCsFixer\\Fixer\\AttributeNotation\\AttributeEmptyParenthesesFixer <./../src/Fixer/AttributeNotation/AttributeEmptyParenthesesFixer.php>`_
