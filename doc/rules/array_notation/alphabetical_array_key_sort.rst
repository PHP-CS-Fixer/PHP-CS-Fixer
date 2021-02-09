====================================
Rule ``alphabetical_array_key_sort``
====================================

Sorts keyed array by alphabetical order.

Configuration
-------------

``sort_special_key_mode``
~~~~~~~~~~~~~~~~~~~~~~~~~

In which way to sort the special keys

Allowed values: ``'special_case_on_bottom'``, ``'special_case_on_top'``

Default value: ``'special_case_on_bottom'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$sample = array('b' => '2', 'a' => '1', 'd' => '5');
   +$sample = array('a' => '1', 'b' => '2', 'd' => '5');

Example #2
~~~~~~~~~~

With configuration: ``['sort_special_key_mode' => 'special_case_on_bottom']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$sample = array('b' => '2', 'a' => '1', foo() => 'bar', 'd' => '5');
   +$sample = array('a' => '1', 'b' => '2', 'd' => '5', foo() => 'bar');

Example #3
~~~~~~~~~~

With configuration: ``['sort_special_key_mode' => 'special_case_on_top']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$sample = array('b' => '2', 'a' => '1', foo() => 'bar', 'd' => '5');
   +$sample = array(foo() => 'bar', 'a' => '1', 'b' => '2', 'd' => '5');
