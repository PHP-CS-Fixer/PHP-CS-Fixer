=========================================================
Rule ``nullable_type_declaration_for_default_null_value``
=========================================================

Adds or removes ``?`` before single type declarations or ``|null`` at the end of
union types when parameters have a default ``null`` value.

Description
-----------

Rule is applied only in a PHP 7.1+ environment.

Configuration
-------------

``use_nullable_type_declaration``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to add or remove ``?`` or ``|null`` to parameters with a default
``null`` value.

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
   -function sample(string $str = null)
   +function sample(?string $str = null)
    {}

Example #2
~~~~~~~~~~

With configuration: ``['use_nullable_type_declaration' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(?string $str = null)
   +function sample(string $str = null)
    {}

Example #3
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(string|int $str = null)
   +function sample(string|int|null $str = null)
    {}

Example #4
~~~~~~~~~~

With configuration: ``['use_nullable_type_declaration' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(string|int|null $str = null)
   +function sample(string|int $str = null)
    {}

Example #5
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(\Foo&\Bar $str = null)
   +function sample((\Foo&\Bar)|null $str = null)
    {}

Example #6
~~~~~~~~~~

With configuration: ``['use_nullable_type_declaration' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample((\Foo&\Bar)|null $str = null)
   +function sample(\Foo&\Bar $str = null)
    {}

Rule sets
---------

The rule is part of the following rule set:

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['use_nullable_type_declaration' => false]``


