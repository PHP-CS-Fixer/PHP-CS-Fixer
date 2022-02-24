================================
Rule ``array_single_multi_line``
================================

Single line arrays that are long or contain many elements must be multiline.

Configuration
-------------

``element_count``
~~~~~~~~~~~~~~~~~

Threshold: # of elements an array must have be be written multiline.

Allowed types: ``int``

Default value: ``25``

``inner_length``
~~~~~~~~~~~~~~~~

Threshold: # of characters there must be between the braces of an array for it
to be made multiline.

Allowed types: ``int``

Default value: ``120``

``conditions``
~~~~~~~~~~~~~~

How the thresholds must be evaluated combined.

Allowed values: ``'all'``, ``'any'``

Default value: ``'any'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25];
   +$a = [
   +1,
   +2,
   +3,
   +4,
   +5,
   +6,
   +7,
   +8,
   +9,
   +10,
   +11,
   +12,
   +13,
   +14,
   +15,
   +16,
   +17,
   +18,
   +19,
   +20,
   +21,
   +22,
   +23,
   +24,
   +25
   +];

Example #2
~~~~~~~~~~

With configuration: ``['element_count' => 2, 'inner_length' => 10000, 'conditions' => 'any']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = [1, 2, 3];
   +$a = [
   +1,
   +2,
   +3
   +];
