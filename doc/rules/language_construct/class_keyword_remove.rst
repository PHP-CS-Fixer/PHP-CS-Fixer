=============================
Rule ``class_keyword_remove``
=============================

`src <../../../src/Fixer/LanguageConstruct/ClassKeywordRemoveFixer.php>`_

Converts ``::class`` keywords to FQCN strings.

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
