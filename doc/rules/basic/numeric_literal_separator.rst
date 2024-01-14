==================================
Rule ``numeric_literal_separator``
==================================

Adds separators to numeric literals of any kind.

Configuration
-------------

``override_existing``
~~~~~~~~~~~~~~~~~~~~~

Whether literals already containing underscores should be reformatted.

Allowed types: ``bool``

Default value: ``false``

``strategy``
~~~~~~~~~~~~

Whether numeric literal should be separated by underscores or not.

Allowed values: ``'no_separator'`` and ``'use_separator'``

Default value: ``'use_separator'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$integer = 1234567890;
   +$integer = 1_234_567_890;

Example #2
~~~~~~~~~~

With configuration: ``['strategy' => 'no_separator']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$integer = 1234_5678;
   -$octal = 01_234_56;
   -$binary = 0b00_10_01_00;
   -$hexadecimal = 0x3D45_8F4F;
   +$integer = 12345678;
   +$octal = 0123456;
   +$binary = 0b00100100;
   +$hexadecimal = 0x3D458F4F;

Example #3
~~~~~~~~~~

With configuration: ``['strategy' => 'use_separator']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$integer = 12345678;
   -$octal = 0123456;
   -$binary = 0b0010010011011010;
   -$hexadecimal = 0x3D458F4F;
   +$integer = 12_345_678;
   +$octal = 0123_456;
   +$binary = 0b00100100_11011010;
   +$hexadecimal = 0x3D_45_8F_4F;

Example #4
~~~~~~~~~~

With configuration: ``['override_existing' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $var = 24_40_21;
   +<?php $var = 244_021;
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Basic\\NumericLiteralSeparatorFixer <./../../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Basic\\NumericLiteralSeparatorFixerTest <./../../../tests/Fixer/Basic/NumericLiteralSeparatorFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
