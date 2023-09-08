=========================
Rule ``class_definition``
=========================

Whitespace around the keywords of a class, trait, enum or interfaces definition
should be one space.

Configuration
-------------

``multi_line_extends_each_single_line``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether definitions should be multiline.

Allowed types: ``bool``

Default value: ``false``

``single_item_single_line``
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether definitions should be single line when including a single item.

Allowed types: ``bool``

Default value: ``false``

``single_line``
~~~~~~~~~~~~~~~

Whether definitions should be single line.

Allowed types: ``bool``

Default value: ``false``

``space_before_parenthesis``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether there should be a single space after the parenthesis of anonymous class
(PSR12) or not.

Allowed types: ``bool``

Default value: ``false``

``inline_constructor_arguments``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether constructor argument list in anonymous classes should be single line.

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

   -class  Foo  extends  Bar  implements  Baz,  BarBaz
   +class Foo extends Bar implements Baz, BarBaz
    {
    }

   -final  class  Foo  extends  Bar  implements  Baz,  BarBaz
   +final class Foo extends Bar implements Baz, BarBaz
    {
    }

   -trait  Foo
   +trait Foo
    {
    }

   -$foo = new  class  extends  Bar  implements  Baz,  BarBaz {};
   +$foo = new class extends Bar implements Baz, BarBaz {};

Example #2
~~~~~~~~~~

With configuration: ``['single_line' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -class Foo
   -extends Bar
   -implements Baz, BarBaz
   +class Foo extends Bar implements Baz, BarBaz
    {}

Example #3
~~~~~~~~~~

With configuration: ``['single_item_single_line' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -class Foo
   -extends Bar
   -implements Baz
   +class Foo extends Bar implements Baz
    {}

Example #4
~~~~~~~~~~

With configuration: ``['multi_line_extends_each_single_line' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    interface Bar extends
   -    Bar, BarBaz, FooBarBaz
   +    Bar,
   +    BarBaz,
   +    FooBarBaz
    {}

Example #5
~~~~~~~~~~

With configuration: ``['space_before_parenthesis' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = new class(){};
   +$foo = new class () {};

Example #6
~~~~~~~~~~

With configuration: ``['inline_constructor_arguments' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = new class(
   -    $bar,
   -    $baz
   -) {};
   +$foo = new class($bar, $baz) {};

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ with config:

  ``['inline_constructor_arguments' => false, 'space_before_parenthesis' => true]``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ with config:

  ``['inline_constructor_arguments' => false, 'space_before_parenthesis' => true]``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ with config:

  ``['inline_constructor_arguments' => false, 'space_before_parenthesis' => true]``

- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['inline_constructor_arguments' => false, 'space_before_parenthesis' => true]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['single_line' => true]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['single_line' => true]``


