======================
Rule ``class_keyword``
======================

EXPERIMENTAL: Converts FQCN strings to ``*::class`` keywords. Do not use it,
unless you know what you are doing.

Description
-----------

This rule does not have an understanding of whether a class exists in the scope
of the codebase or not, relying on run-time and autoloaded classes to determine
it, which makes the rule useless when running on a single file out of codebase
context.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky as EXPERIMENTAL.

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
Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\ClassKeywordFixer <./../../../src/Fixer/LanguageConstruct/ClassKeywordFixer.php>`_
