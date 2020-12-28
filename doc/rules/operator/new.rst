============
Rule ``new``
============

All instances created with new keyword must be followed by braces (or not).

Configuration
-------------

``with_braces``
~~~~~~~~~~~~~~~

Whether new should be used with braces.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $x = new X;
   +<?php $x = new X();

Example #2
~~~~~~~~~~

With configuration: ``['with_braces' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $x = new X;
   +<?php $x = new X();

Example #3
~~~~~~~~~~

With configuration: ``['with_braces' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php $x = new X();
   +<?php $x = new X;
