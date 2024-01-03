====================================
Rule ``method_chaining_indentation``
====================================

Method chaining MUST be properly indented. Method chaining with different levels
of indentation is not supported.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $user->setEmail('voff.web@gmail.com')
   -         ->setPassword('233434');
   +    ->setPassword('233434');

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\MethodChainingIndentationFixer <./../../../src/Fixer/Whitespace/MethodChainingIndentationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\MethodChainingIndentationFixerTest <./../../../tests/Fixer/Whitespace/MethodChainingIndentationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
