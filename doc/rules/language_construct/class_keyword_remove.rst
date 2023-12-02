=============================
Rule ``class_keyword_remove``
=============================

Converts ``::class`` keywords to FQCN strings.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

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
Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\ClassKeywordRemoveFixer <./../../../src/Fixer/LanguageConstruct/ClassKeywordRemoveFixer.php>`_
