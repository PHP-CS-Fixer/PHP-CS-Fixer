==========================
Rule ``heredoc_to_nowdoc``
==========================

Convert ``heredoc`` to ``nowdoc`` where possible.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = <<<"TEST"
   +<?php $a = <<<'TEST'
    Foo
    TEST;

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``heredoc_to_nowdoc`` rule.
