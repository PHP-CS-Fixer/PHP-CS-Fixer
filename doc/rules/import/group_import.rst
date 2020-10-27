=====================
Rule ``group_import``
=====================

There MUST be group use for the same namespaces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,2 @@
    <?php
   -use Foo\Bar;
   -use Foo\Baz;
   +use Foo\{Bar, Baz};
