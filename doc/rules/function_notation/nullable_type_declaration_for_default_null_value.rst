=========================================================
Rule ``nullable_type_declaration_for_default_null_value``
=========================================================

Adds or removes ``?`` before single type declarations or ``|null`` at the end of
union types when parameters have a default ``null`` value.

Configuration
-------------

``use_nullable_type_declaration``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. warning:: This option is deprecated and will be removed in the next major version. Behaviour will follow default one.

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

The rule is part of the following rule sets:

- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_
- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ *(deprecated)*
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ *(deprecated)*
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\NullableTypeDeclarationForDefaultNullValueFixer <./../../../src/Fixer/FunctionNotation/NullableTypeDeclarationForDefaultNullValueFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\NullableTypeDeclarationForDefaultNullValueFixerTest <./../../../tests/Fixer/FunctionNotation/NullableTypeDeclarationForDefaultNullValueFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
