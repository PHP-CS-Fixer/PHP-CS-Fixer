====================
Rule ``cast_spaces``
====================

A single space or none should be between cast and variable.

Configuration
-------------

``space``
~~~~~~~~~

Spacing to apply between cast and variable.

Allowed values: ``'none'`` and ``'single'``

Default value: ``'single'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$bar = ( string )  $a;
   -$foo = (int)$b;
   +$bar = (string) $a;
   +$foo = (int) $b;

Example #2
~~~~~~~~~~

With configuration: ``['space' => 'single']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$bar = ( string )  $a;
   -$foo = (int)$b;
   +$bar = (string) $a;
   +$foo = (int) $b;

Example #3
~~~~~~~~~~

With configuration: ``['space' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$bar = ( string )  $a;
   -$foo = (int) $b;
   +$bar = (string)$a;
   +$foo = (int)$b;

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)*
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\CastNotation\\CastSpacesFixer <./../../../src/Fixer/CastNotation/CastSpacesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\CastNotation\\CastSpacesFixerTest <./../../../tests/Fixer/CastNotation/CastSpacesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
