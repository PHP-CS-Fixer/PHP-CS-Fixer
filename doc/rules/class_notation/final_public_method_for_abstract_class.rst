===============================================
Rule ``final_public_method_for_abstract_class``
===============================================

All ``public`` methods of ``abstract`` classes should be ``final``.

Description
-----------

Enforce API encapsulation in an inheritance architecture. If you want to
override a method, use the Template method pattern.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\FinalPublicMethodForAbstractClassFixer <./../../../src/Fixer/ClassNotation/FinalPublicMethodForAbstractClassFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\FinalPublicMethodForAbstractClassFixerTest <./../../../tests/Fixer/ClassNotation/FinalPublicMethodForAbstractClassFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
