====================================
Rule ``alphabetical_array_key_sort``
====================================

Sorts keyed arrays alphabetically.

Description
-----------

Alphabetically sorts any keyed array on its key values.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the order of the array has an impact on the code execution.

Configuration
-------------

``sort_special_key_mode``
~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to sort the specials keys on the bottom ``special_case_on_bottom`` or
top ``special_case_on_top``.

Allowed values: ``'special_case_on_bottom'`` and ``'special_case_on_top'``

Default value: ``'special_case_on_bottom'``

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

With configuration: ``['sort_special_key_mode' => 'special_case_on_bottom']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = ['b' => '2', 'a' => '1', foo() => 'bar', 'd' => '5'];
   +$sample = ['a' => '1', 'b' => '2', 'd' => '5', foo() => 'bar'];

Example #4
~~~~~~~~~~

With configuration: ``['sort_special_key_mode' => 'special_case_on_top']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = ['b' => '2', 'a' => '1', foo() => 'bar', 'd' => '5'];
   +$sample = [foo() => 'bar', 'a' => '1', 'b' => '2', 'd' => '5'];
Source class
------------

`PhpCsFixer\\Fixer\\ArrayNotation\\AlphabeticalArrayKeySortFixer <./../../../src/Fixer/ArrayNotation/AlphabeticalArrayKeySortFixer.php>`_
