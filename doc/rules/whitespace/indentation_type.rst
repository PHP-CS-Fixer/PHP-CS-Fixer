=========================
Rule ``indentation_type``
=========================

Code MUST use configured indentation type.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    if (true) {
   -<TAB>echo 'Hello!';
   +    echo 'Hello!';
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\IndentationTypeFixer <./../../../src/Fixer/Whitespace/IndentationTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\IndentationTypeFixerTest <./../../../tests/Fixer/Whitespace/IndentationTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
