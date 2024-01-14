======================
Rule ``class_keyword``
======================

Converts FQCN strings to ``*::class`` keywords.

Description
-----------

This rule does not have an understanding of whether a class exists in the scope
of the codebase or not, relying on run-time and autoloaded classes to determine
it, which makes the rule useless when running on a single file out of codebase
context.

Warning
-------

This rule is experimental
~~~~~~~~~~~~~~~~~~~~~~~~~

It is not covered with backward compatibility promise and may produce unstable
or unexpected results.

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Do not use it, unless you know what you are doing.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$foo = 'PhpCsFixer\Tokenizer\Tokens';
   -$bar = "\PhpCsFixer\Tokenizer\Tokens";
   +$foo = \PhpCsFixer\Tokenizer\Tokens::class;
   +$bar = \PhpCsFixer\Tokenizer\Tokens::class;
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\ClassKeywordFixer <./../../../src/Fixer/LanguageConstruct/ClassKeywordFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\ClassKeywordFixerTest <./../../../tests/Fixer/LanguageConstruct/ClassKeywordFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
