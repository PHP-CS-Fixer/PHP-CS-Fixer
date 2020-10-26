============================
Rule ``magic_method_casing``
============================

Magic method definitions and calls must be using the correct casing.

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
   -    public function __Sleep()
   +    public function __sleep()
        {
        }
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$foo->__INVOKE(1);
   +$foo->__invoke(1);

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``magic_method_casing`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``magic_method_casing`` rule.
