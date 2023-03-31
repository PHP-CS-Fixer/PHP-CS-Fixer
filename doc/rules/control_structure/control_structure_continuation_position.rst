================================================
Rule ``control_structure_continuation_position``
================================================

Control structure continuation keyword must be on the configured line.

Configuration
-------------

``position``
~~~~~~~~~~~~

The position of the keyword that continues the control structure.

Allowed values: ``'next_line'``, ``'same_line'``

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

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``control_structure_continuation_position`` rule with the default config.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``control_structure_continuation_position`` rule with the default config.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``control_structure_continuation_position`` rule with the default config.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``control_structure_continuation_position`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``control_structure_continuation_position`` rule with the default config.
