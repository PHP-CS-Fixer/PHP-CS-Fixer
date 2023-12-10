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

Source class
------------

`PhpCsFixer\\Fixer\\Operator\\NoUselessNullsafeOperatorFixer <./../../../src/Fixer/Operator/NoUselessNullsafeOperatorFixer.php>`_
