==============================
Rule ``static_private_method``
==============================

Converts private methods to ``static`` where possible.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when method contains dynamic generated calls to the instance, or the
method is dynamically referenced.

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
Source class
------------

`PhpCsFixer\\Fixer\\ClassNotation\\StaticPrivateMethodFixer <./../../../src/Fixer/ClassNotation/StaticPrivateMethodFixer.php>`_
