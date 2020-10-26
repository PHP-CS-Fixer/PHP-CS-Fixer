=========================
Rule ``class_definition``
=========================

Whitespace around the keywords of a class, trait or interfaces definition should
be one space.

Configuration
-------------

``multi_line_extends_each_single_line``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether definitions should be multiline.

.. note:: The previous name of this option was ``multiLineExtendsEachSingleLine`` but it is now deprecated and will be removed on next major version.

Allowed types: ``bool``

Default value: ``false``

``single_item_single_line``
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether definitions should be single line when including a single item.

.. note:: The previous name of this option was ``singleItemSingleLine`` but it is now deprecated and will be removed on next major version.

Allowed types: ``bool``

Default value: ``false``

``single_line``
~~~~~~~~~~~~~~~

Whether definitions should be single line.

.. note:: The previous name of this option was ``singleLine`` but it is now deprecated and will be removed on next major version.

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
   @@ -1,13 +1,13 @@
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

Example #2
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php

   -$foo = new  class  extends  Bar  implements  Baz,  BarBaz {};
   +$foo = new class extends Bar implements Baz, BarBaz {};

Example #3
~~~~~~~~~~

With configuration: ``['single_line' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,6 +1,4 @@
    <?php

   -class Foo
   -extends Bar
   -implements Baz, BarBaz
   +class Foo extends Bar implements Baz, BarBaz
    {}

Example #4
~~~~~~~~~~

With configuration: ``['single_item_single_line' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,6 +1,4 @@
    <?php

   -class Foo
   -extends Bar
   -implements Baz
   +class Foo extends Bar implements Baz
    {}

Example #5
~~~~~~~~~~

With configuration: ``['multi_line_extends_each_single_line' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,7 @@
    <?php

    interface Bar extends
   -    Bar, BarBaz, FooBarBaz
   +    Bar,
   +    BarBaz,
   +    FooBarBaz
    {}

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``class_definition`` rule with the default config.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``class_definition`` rule with the config below:

  ``['single_line' => true]``

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``class_definition`` rule with the config below:

  ``['single_line' => true]``
