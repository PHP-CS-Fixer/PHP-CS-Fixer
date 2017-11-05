==========================
Rule ``method_separation``
==========================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``class_attributes_separation`` instead.

Methods must be separated with one blank line.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -4,7 +4,8 @@
        protected function foo()
        {
        }
   +
        protected function bar()
        {
        }
    }
