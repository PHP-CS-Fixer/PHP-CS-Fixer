================================
Rule ``return_type_declaration``
================================

Adjust spacing around colon in return type declarations and backed enum types.

Warnings
--------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``colon_space`` instead.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``space_before``.

Configuration
-------------

``space_before``
~~~~~~~~~~~~~~~~

Spacing to apply before colon.

Allowed values: ``'none'`` and ``'one'``

Default value: ``'none'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo(int $a):string {};
   +function foo(int $a): string {};

Example #2
~~~~~~~~~~

With configuration: ``['space_before' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo(int $a):string {};
   +function foo(int $a): string {};

Example #3
~~~~~~~~~~

With configuration: ``['space_before' => 'one']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo(int $a):string {};
   +function foo(int $a) : string {};

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\ReturnTypeDeclarationFixer <./../../../src/Fixer/FunctionNotation/ReturnTypeDeclarationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\ReturnTypeDeclarationFixerTest <./../../../tests/Fixer/FunctionNotation/ReturnTypeDeclarationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
