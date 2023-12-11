======================
Rule ``class_keyword``
======================

EXPERIMENTAL: Converts FQCN strings to ``*::class`` keywords. Do not use it,
unless you know what you are doing.

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
