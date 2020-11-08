============================
Rule ``lowercase_constants``
============================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``constant_case`` instead.

The PHP constants ``true``, ``false``, and ``null`` MUST be in lower case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
    <?php
   -$a = FALSE;
   -$b = True;
   -$c = nuLL;
   +$a = false;
   +$b = true;
   +$c = null;
