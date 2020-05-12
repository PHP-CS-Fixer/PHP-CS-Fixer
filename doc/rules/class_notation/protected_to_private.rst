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

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``protected_to_private`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``protected_to_private`` rule.
