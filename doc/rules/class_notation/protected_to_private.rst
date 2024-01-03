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

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\ProtectedToPrivateFixer <./../../../src/Fixer/ClassNotation/ProtectedToPrivateFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ProtectedToPrivateFixerTest <./../../../tests/Fixer/ClassNotation/ProtectedToPrivateFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
