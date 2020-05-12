=========================================================
Rule ``nullable_type_declaration_for_default_null_value``
=========================================================

Adds or removes ``?`` before type declarations for parameters with a default
``null`` value.

Description
-----------

Rule is applied only in a PHP 7.1+ environment.

Configuration
-------------

``use_nullable_type_declaration``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to add or remove ``?`` before type declarations for parameters with a
default ``null`` value.

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
