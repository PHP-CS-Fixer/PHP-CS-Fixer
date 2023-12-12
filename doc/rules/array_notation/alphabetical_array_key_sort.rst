====================================
Rule ``alphabetical_array_key_sort``
====================================

Sorts keyed arrays alphabetically.

Description
-----------

Use this fixer when you want a standardised representation of your keyed arrays;
it will sort the numeric and string keys of your arrays alphabetically. Special
keys, like concatenated ones, are kept in the same order but are either moved to
the top or bottom (default) according to the configuration option.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the order of the array has an impact on the code execution.

Configuration
-------------

``special_keys_placement``
~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to sort the specials keys on the bottom ``bottom`` or top ``top``.

Allowed values: ``'bottom'`` and ``'top'``

Default value: ``'bottom'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = array('b' => '2', 'a' => '1', 'd' => '5');
   +$sample = array('a' => '1', 'b' => '2', 'd' => '5');

Example #2
~~~~~~~~~~

With configuration: ``[]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = ['b' => '2', 'a' => '1', 'd' => '5'];
   +$sample = ['a' => '1', 'b' => '2', 'd' => '5'];

Example #3
~~~~~~~~~~

With configuration: ``['special_keys_placement' => 'bottom']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = ['b' => '2', 'a' => '1', foo() => 'bar', 'd' => '5'];
   +$sample = ['a' => '1', 'b' => '2', 'd' => '5', foo() => 'bar'];

Example #4
~~~~~~~~~~

With configuration: ``['special_keys_placement' => 'top']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = ['b' => '2', 'a' => '1', foo() => 'bar', 'd' => '5'];
   +$sample = [foo() => 'bar', 'a' => '1', 'b' => '2', 'd' => '5'];
Source class
------------

`PhpCsFixer\\Fixer\\ArrayNotation\\AlphabeticalArrayKeySortFixer <./../../../src/Fixer/ArrayNotation/AlphabeticalArrayKeySortFixer.php>`_
