=============================
Rule ``multiline_long_array``
=============================

A single-line array should be broken into multiple lines if it exceeds
configured limit.

Configuration
-------------

``max_length``
~~~~~~~~~~~~~~

Maximum length for single-line arrays. 0 : multi-line only, -1 : single-line
only.

Allowed types: ``int``

Default value: ``0``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$array = ['a very very long element','another very long element'];
   +$array = [
   +'a very very long element',
   +'another very long element'
   +];

Example #2
~~~~~~~~~~

With configuration: ``['max_length' => 10]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$array = ['a very very long element','another very long element'];
   +$array = [
   +'a very very long element',
   +'another very long element'
   +];
