====================
Rule ``list_syntax``
====================

List (``array`` destructuring) assignment should be declared using the
configured syntax. Requires PHP >= 7.1.

Configuration
-------------

``syntax``
~~~~~~~~~~

Whether to use the ``long`` or ``short`` ``list`` syntax.

Allowed values: ``'long'``, ``'short'``

Default value: ``'long'``

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
   -[$sample] = $array;
   +list($sample) = $array;

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'short']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -list($sample) = $array;
   +[$sample] = $array;
