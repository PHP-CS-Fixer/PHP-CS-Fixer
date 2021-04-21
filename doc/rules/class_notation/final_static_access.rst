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
    <?php
    final class Sample
    {
        public function getFoo()
        {
   -        return static::class;
   +        return self::class;
        }
    }
