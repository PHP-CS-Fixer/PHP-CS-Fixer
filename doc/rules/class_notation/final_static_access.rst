============================
Rule ``final_static_access``
============================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``self_static_accessor`` instead.

Converts ``static`` access to ``self`` access in ``final`` classes.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,6 +3,6 @@
    {
        public function getFoo()
        {
   -        return static::class;
   +        return self::class;
        }
    }
