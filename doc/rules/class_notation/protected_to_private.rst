=============================
Rule ``protected_to_private``
=============================

Converts ``protected`` variables and methods to ``private`` where possible.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,9 +1,9 @@
    <?php
    final class Sample
    {
   -    protected $a;
   +    private $a;

   -    protected function test()
   +    private function test()
        {
        }
    }

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``protected_to_private`` rule.
