================================================
Rule ``control_structure_continuation_position``
================================================

Control structure continuation keyword must be on the configured line.

Configuration
-------------

``position``
~~~~~~~~~~~~

the position of the keyword that continues the control structure.

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
