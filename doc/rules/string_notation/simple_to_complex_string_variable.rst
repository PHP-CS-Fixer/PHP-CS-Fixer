==========================================
Rule ``simple_to_complex_string_variable``
==========================================

Converts explicit variables in double-quoted strings and heredoc syntax from
simple to complex format (``${`` to ``{$``).

Description
-----------

Doesn't touch implicit variables. Works together nicely with
``explicit_string_variable``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $name = 'World';
   -echo "Hello ${name}!";
   +echo "Hello {$name}!";

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $name = 'World';
    echo <<<TEST
   -Hello ${name}!
   +Hello {$name}!
    TEST;

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_
- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_
- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_
- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ *(deprecated)*
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_ *(deprecated)*
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ *(deprecated)*
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ *(deprecated)*
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\StringNotation\\SimpleToComplexStringVariableFixer <./../../../src/Fixer/StringNotation/SimpleToComplexStringVariableFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\StringNotation\\SimpleToComplexStringVariableFixerTest <./../../../tests/Fixer/StringNotation/SimpleToComplexStringVariableFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
