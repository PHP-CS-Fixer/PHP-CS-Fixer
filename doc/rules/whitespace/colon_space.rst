====================
Rule ``colon_space``
====================

Adjust spacing around the colon in return type declarations, backed enum types,
named arguments, and switch case statements.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``space_before_case``,
``space_before_named_argument``, ``space_before_return_type``.

Configuration
-------------

``space_before_case``
~~~~~~~~~~~~~~~~~~~~~

Spacing to apply before the colon of a ``case`` or ``default`` statement.

Allowed values: ``'none'`` and ``'one'``

Default value: ``'none'``

``space_before_named_argument``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Spacing to apply before the colon of a named argument.

Allowed values: ``'none'`` and ``'one'``

Default value: ``'none'``

``space_before_return_type``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Spacing to apply before the colon of a return type declaration or backed enum
type.

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
   -    function foo(int $a):string {}
   +    function foo(int $a): string {}

        switch($a) {
   -        case 1   :
   +        case 1:
                break;
   -        default     :
   +        default:
                return 2;
        }

Example #2
~~~~~~~~~~

With configuration: ``['space_before_return_type' => 'one']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo(int $a):string {}
   +function foo(int $a) : string {}

Example #3
~~~~~~~~~~

With configuration: ``['space_before_case' => 'one']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    switch ($a) {
   -    case 1:
   +    case 1 :
            break;
    }

Example #4
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -foo(bar : 'baz');
   +foo(bar: 'baz');

Example #5
~~~~~~~~~~

With configuration: ``['space_before_named_argument' => 'one']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -foo(bar: 'baz');
   +foo(bar : 'baz');

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ *(deprecated)*
- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)*
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\ColonSpaceFixer <./../../../src/Fixer/Whitespace/ColonSpaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\ColonSpaceFixerTest <./../../../tests/Fixer/Whitespace/ColonSpaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
