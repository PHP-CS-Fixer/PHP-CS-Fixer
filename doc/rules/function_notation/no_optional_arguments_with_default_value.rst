=================================================
Rule ``no_optional_arguments_with_default_value``
=================================================

Remove arguments whose value is their default’s. Requires PHP >= 8.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php chunk_split("Second argument has its default value so it can be removed.", 76);
   +<?php chunk_split("Second argument has its default value so it can be removed.");

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php chunk_split("Third argument must be kept so it will be named.", 76, "\n");
   +<?php chunk_split("Third argument must be kept so it will be named.", separator: "\n");
