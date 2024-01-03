=====================
Rule ``dir_constant``
=====================

Replaces ``dirname(__FILE__)`` expression with equivalent ``__DIR__`` constant.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the function ``dirname`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = dirname(__FILE__);
   +$a = __DIR__;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\DirConstantFixer <./../../../src/Fixer/LanguageConstruct/DirConstantFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\DirConstantFixerTest <./../../../tests/Fixer/LanguageConstruct/DirConstantFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
