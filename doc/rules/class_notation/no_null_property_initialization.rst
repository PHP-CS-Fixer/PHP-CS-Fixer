========================================
Rule ``no_null_property_initialization``
========================================

Properties MUST not be explicitly initialized with ``null`` except when they
have a type declaration (PHP 7.4).

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
   -    public $foo = null;
   +    public $foo;
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
   -    public static $foo = null;
   +    public static $foo;
    }

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_null_property_initialization`` rule.
