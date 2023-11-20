==========================================
Rule ``date_time_create_from_format_call``
==========================================

The first argument of ``DateTime::createFromFormat`` method must start with
``!``.

Description
-----------

Consider this code:
    ``DateTime::createFromFormat('Y-m-d', '2022-02-11')``.
    What value will be returned? '2022-02-11 00:00:00.0'? No, actual return
value has 'H:i:s' section like '2022-02-11 16:55:37.0'.
    Change 'Y-m-d' to '!Y-m-d', return value will be '2022-02-11 00:00:00.0'.
    So, adding ``!`` to format string will make return value more intuitive.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when depending on the actual timings being used even when not explicit set
in format.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php \DateTime::createFromFormat('Y-m-d', '2022-02-11');
   +<?php \DateTime::createFromFormat('!Y-m-d', '2022-02-11');
Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\DateTimeCreateFromFormatCallFixer <./../src/Fixer/FunctionNotation/DateTimeCreateFromFormatCallFixer.php>`_
