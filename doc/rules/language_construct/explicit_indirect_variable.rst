===================================
Rule ``explicit_indirect_variable``
===================================

Add curly braces to indirect variables to make them clear to understand.
Requires PHP >= 7.0.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -echo $$foo;
   -echo $$foo['bar'];
   -echo $foo->$bar['baz'];
   -echo $foo->$callback($baz);
   +echo ${$foo};
   +echo ${$foo}['bar'];
   +echo $foo->{$bar}['baz'];
   +echo $foo->{$callback}($baz);

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\ExplicitIndirectVariableFixer <./../../../src/Fixer/LanguageConstruct/ExplicitIndirectVariableFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\ExplicitIndirectVariableFixerTest <./../../../tests/Fixer/LanguageConstruct/ExplicitIndirectVariableFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
