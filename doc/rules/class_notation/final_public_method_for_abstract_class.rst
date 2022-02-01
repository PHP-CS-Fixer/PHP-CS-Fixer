===============================================
Rule ``final_public_method_for_abstract_class``
===============================================

All ``public`` methods of ``abstract`` classes should be ``final``.

Description
-----------

Enforce API encapsulation in an inheritance architecture. If you want to
override a method, use the Template method pattern.

.. warning:: Using this rule is risky.

   Risky when overriding ``public`` methods of ``abstract`` classes.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    abstract class AbstractMachine
    {
   -    public function start()
   +    final public function start()
        {}
    }
