=============================
Rule ``class_keyword_remove``
=============================

Converts ``::class`` keywords to FQCN strings.

Warning
-------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

No replacement available.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    use Foo\Bar\Baz;

   -$className = Baz::class;
   +$className = 'Foo\Bar\Baz';

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\ClassKeywordRemoveFixer <./../../../src/Fixer/LanguageConstruct/ClassKeywordRemoveFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\ClassKeywordRemoveFixerTest <./../../../tests/Fixer/LanguageConstruct/ClassKeywordRemoveFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
