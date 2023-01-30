==================================
Rule ``numeric_literal_separator``
==================================

Adds separators to numeric literals of any kind.

Configuration
-------------

``override_existing``
~~~~~~~~~~~~~~~~~~~~~

Reformat literals already contain underscores.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$integer = 12345678;
   -$octal = 0o123456;
   -$binary = 0b00100100;
   -$hexadecimal = 0x3D458F4F;
   +$integer = 12_345_678;
   +$octal = 0o123_456;
   +$binary = 0b0010_0100;
   +$hexadecimal = 0x3D_45_8F_4F;

Example #2
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
