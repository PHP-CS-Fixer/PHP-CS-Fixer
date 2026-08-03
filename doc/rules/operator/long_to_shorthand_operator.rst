===================================
Rule ``long_to_shorthand_operator``
===================================

Shorthand notation for operators should be used if possible.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when applying for string offsets (e.g. ``<?php $text = "foo"; $text[0] =
$text[0] & "\x7F";``).

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``skip_offset_targets``.

Configuration
-------------

``skip_offset_targets``
~~~~~~~~~~~~~~~~~~~~~~~

Whether to leave assignments to an offset (e.g. ``$a[0] = $a[0] + 1;``)
untouched. Shortening them is only safe when the offset belongs to an array or
to an object implementing ``ArrayAccess``; if it is a string offset, PHP fails
at runtime with "Cannot use assign-op operators with string offsets". The two
cannot be told apart from the source, so offsets are skipped by default.

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
   -$i = $i + 10;
   +$i += 10;

Example #2
~~~~~~~~~~

With configuration: ``['skip_offset_targets' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $text = "foo";
   -$text[0] = $text[0] & "\x7F";
   -$numbers[0] = $numbers[0] + 1;
   +$text[0] &= "\x7F";
   +$numbers[0] += 1;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\LongToShorthandOperatorFixer <./../../../src/Fixer/Operator/LongToShorthandOperatorFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\LongToShorthandOperatorFixerTest <./../../../tests/Fixer/Operator/LongToShorthandOperatorFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
