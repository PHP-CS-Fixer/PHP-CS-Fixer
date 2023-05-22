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
   -function sample(?string $str = null)
   +function sample(string|null $str = null)
    {}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
   -  private ?string $str = null;
   +  private string|null $str = null;
    }

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(): ?string
   +function sample(): string|null
    {}

Example #4
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$fn = fn (): ?int => 5;
   +$fn = fn (): int|null => 5;
