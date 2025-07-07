==============================
Rule ``static_private_method``
==============================

Converts private methods to ``static`` where possible.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the method: contains dynamic generated calls to the instance, is
dynamically referenced, is referenced inside a Trait the class uses.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo
    {
        public function bar()
        {
   -        return $this->baz();
   +        return self::baz();
        }

   -    private function baz()
   +    private static function baz()
        {
            return 1;
        }
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\StaticPrivateMethodFixer <./../../../src/Fixer/ClassNotation/StaticPrivateMethodFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\StaticPrivateMethodFixerTest <./../../../tests/Fixer/ClassNotation/StaticPrivateMethodFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
