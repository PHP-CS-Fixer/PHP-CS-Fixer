===================
Rule ``union_null``
===================

Replaces ? with the corresponding union type.

Description
-----------

Rule is applied only in a PHP 8.0+ environment.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo(?int $a = null) {}
   +function foo(int|null $a = null) {}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
   -  public ?int $foo;
   +  public int|null $foo;
    }
