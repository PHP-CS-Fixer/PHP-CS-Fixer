=====================================
Rule ``no_useless_nullsafe_operator``
=====================================

There should not be useless Null-safe operator ``?->`` used.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo extends Bar
    {
        public function test() {
   -        echo $this?->parentMethod();
   +        echo $this->parentMethod();
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NoUselessNullsafeOperatorFixer <./../../../src/Fixer/Operator/NoUselessNullsafeOperatorFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NoUselessNullsafeOperatorFixerTest <./../../../tests/Fixer/Operator/NoUselessNullsafeOperatorFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
