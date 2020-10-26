============================
Rule ``no_php4_constructor``
============================

Convert PHP4-style constructors to ``__construct``.

.. warning:: Using this rule is risky.

   Risky when old style constructor being fixed is overridden or overrides
   parent one.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,7 @@
    <?php
    class Foo
    {
   -    public function Foo($bar)
   +    public function __construct($bar)
        {
        }
    }
