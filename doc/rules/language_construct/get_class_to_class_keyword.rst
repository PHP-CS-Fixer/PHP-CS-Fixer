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
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\GetClassToClassKeywordFixer <./../src/Fixer/LanguageConstruct/GetClassToClassKeywordFixer.php>`_
