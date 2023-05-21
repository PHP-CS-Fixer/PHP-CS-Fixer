================
Rule ``union_null``
================

Replaces ``?int`` types with ``int|null``.

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
