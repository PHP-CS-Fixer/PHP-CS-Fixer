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

- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\StringNotation\\SimpleToComplexStringVariableFixer <./../../../src/Fixer/StringNotation/SimpleToComplexStringVariableFixer.php>`_
