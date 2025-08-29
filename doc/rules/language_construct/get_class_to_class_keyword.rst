===================================
Rule ``get_class_to_class_keyword``
===================================

Replace ``get_class`` calls on object variables with class keyword syntax.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

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

- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\GetClassToClassKeywordFixer <./../../../src/Fixer/LanguageConstruct/GetClassToClassKeywordFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\GetClassToClassKeywordFixerTest <./../../../tests/Fixer/LanguageConstruct/GetClassToClassKeywordFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
