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

Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\ExplicitIndirectVariableFixer <./../../../src/Fixer/LanguageConstruct/ExplicitIndirectVariableFixer.php>`_
