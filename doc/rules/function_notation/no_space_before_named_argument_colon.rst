=============================================
Rule ``no_space_before_named_argument_colon``
=============================================

There must be no space before named arguments colons.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -foo(bar : 'baz', qux /* corge */ : 3);
   +foo(bar: 'baz', qux/* corge */: 3);

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\NoSpaceBeforeNamedArgumentColonFixer <./../../../src/Fixer/FunctionNotation/NoSpaceBeforeNamedArgumentColonFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\NoSpaceBeforeNamedArgumentColonFixerTest <./../../../tests/Fixer/FunctionNotation/NoSpaceBeforeNamedArgumentColonFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
