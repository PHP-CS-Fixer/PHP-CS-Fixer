==========================
Rule ``comment_to_phpdoc``
==========================

Comments with annotation should be docblock when used on structural elements.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky as new docblocks might mean more, e.g. a Doctrine entity might have a new
column in database.

Configuration
-------------

``ignored_tags``
~~~~~~~~~~~~~~~~

List of ignored tags

Allowed types: ``array``

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

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``comment_to_phpdoc`` rule with the default config.
