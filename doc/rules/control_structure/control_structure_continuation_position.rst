================================================
Rule ``control_structure_continuation_position``
================================================

Control structure continuation keyword must be on the configured line.

Configuration
-------------

``position``
~~~~~~~~~~~~

The position of the keyword that continues the control structure.

Allowed values: ``'next_line'`` and ``'same_line'``

Default value: ``'same_line'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if ($baz == true) {
        echo "foo";
   -}
   -else {
   +} else {
        echo "bar";
    }

Example #2
~~~~~~~~~~

With configuration: ``['position' => 'next_line']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if ($baz == true) {
        echo "foo";
   -} else {
   +}
   +else {
        echo "bar";
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\ControlStructureContinuationPositionFixer <./../../../src/Fixer/ControlStructure/ControlStructureContinuationPositionFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\ControlStructureContinuationPositionFixerTest <./../../../tests/Fixer/ControlStructure/ControlStructureContinuationPositionFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
