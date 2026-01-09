===================================
Rule ``get_class_to_class_keyword``
===================================

Replace ``get_class`` calls on object variables with class keyword syntax.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky if the ``get_class`` function is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -get_class($a);
   +$a::class;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    $date = new \DateTimeImmutable();
   -$class = get_class($date);
   +$class = $date::class;

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_
- `@PHP8x1Migration:risky <./../../ruleSets/PHP8x1MigrationRisky.rst>`_
- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP8x3Migration:risky <./../../ruleSets/PHP8x3MigrationRisky.rst>`_
- `@PHP8x4Migration:risky <./../../ruleSets/PHP8x4MigrationRisky.rst>`_
- `@PHP8x5Migration:risky <./../../ruleSets/PHP8x5MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ *(deprecated)*
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\GetClassToClassKeywordFixer <./../../../src/Fixer/LanguageConstruct/GetClassToClassKeywordFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\GetClassToClassKeywordFixerTest <./../../../tests/Fixer/LanguageConstruct/GetClassToClassKeywordFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
