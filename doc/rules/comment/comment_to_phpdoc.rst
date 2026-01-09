==========================
Rule ``comment_to_phpdoc``
==========================

Comments with annotation should be docblock when used on structural elements.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky as new docblocks might mean more, e.g. a Doctrine entity might have a new
column in database.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``ignored_tags``.

Configuration
-------------

``ignored_tags``
~~~~~~~~~~~~~~~~

List of ignored tags.

Allowed types: ``list<string>``

Default value: ``[]``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php /* header */ $x = true; /* @var bool $isFoo */ $isFoo = true;
   +<?php /* header */ $x = true; /** @var bool $isFoo */ $isFoo = true;

Example #2
~~~~~~~~~~

With configuration: ``['ignored_tags' => ['todo']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    // @todo do something later
    $foo = 1;

   -// @var int $a
   +/** @var int $a */
    $a = foo();

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Comment\\CommentToPhpdocFixer <./../../../src/Fixer/Comment/CommentToPhpdocFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Comment\\CommentToPhpdocFixerTest <./../../../tests/Fixer/Comment/CommentToPhpdocFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
